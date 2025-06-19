````markdown
# Website-Wide Search System

A Laravel 12 application implementing a website-wide search system with MySQL, Laravel Scout (Database driver), Laravel Queues (Redis), and Bootstrap 5 for the frontend.

## Setup Instructions

1. **Clone the Repository**:
   ```bash
   git clone https://github.com/RatheeshN/website-search.git
   cd website-search-system
   ```
````

2. **Install Dependencies**:
   Install PHP and JavaScript dependencies:

   ```bash
   docker-compose exec app composer install
   docker-compose exec app npm install
   docker-compose exec app npm run build
   ```

3. **Set Up Environment**:

   - Copy `.env.example` to `.env`:
     ```bash
     cp .env.example .env
     ```
   - Update `.env` with the following:
     ```env
     APP_ENV=local
     APP_DEBUG=true
     DB_CONNECTION=mysql
     DB_HOST=localhost
     DB_PORT=3306
     DB_DATABASE=website_search
     DB_USERNAME=root
     DB_PASSWORD=
     SCOUT_DRIVER=database
     SCOUT_QUEUE=true
     QUEUE_CONNECTION=redis
     REDIS_HOST=redis
     REDIS_PORT=6379
     ```
   - Generate the application key:
     ```bash
     docker-compose exec app php artisan key:generate
     ```

4. **Run Docker**:
   Start the Docker containers:

   ```bash
   docker-compose up -d
   ```

5. **Run Migrations and Seeders**:
   Create database tables and populate with sample data:

   ```bash
   docker-compose exec app php artisan migrate
   docker-compose exec app php artisan db:seed
   ```

6. **Rebuild Search Index**:
   Index all models for search:

   ```bash
   docker-compose exec app php artisan scout:rebuild
   ```

7. **Run Queue Worker**:
   Process indexing jobs:

   ```bash
   docker-compose exec app php artisan queue:work
   ```

8. **Access the Application**:
   - Open `http://localhost:8000` in a browser to use the search interface.
   - Test API endpoints using tools like Postman.

## Indexing and Search Logic

### Indexing

- **Laravel Scout**: The application uses Laravel Scout with the Database driver for indexing searchable content.
- **Models**: The `BlogPost`, `Product`, `Page`, and `Faq` models implement the `Searchable` trait, defining searchable fields (`title`, `body`, `tags`, etc.) in the `toSearchableArray` method.
- **Queue-Based Indexing**: The `UpdateSearchIndex` job is dispatched on model create, update, or delete events, ensuring asynchronous index updates via Laravel Queues (Redis).
- **Manual Rebuild**: The `php artisan scout:rebuild` command flushes and re-imports all models into the search index.

### Search Logic

- **Unified Search Endpoint**: The `/api/search?q=...` endpoint searches across all models using Scout’s search functionality.
  - Supports partial matching (e.g., “deve” matches “developer”).
  - Returns paginated results (10 per page) with metadata: `type` (e.g., BlogPost), `title`, `snippet` (truncated content), and `link` (URL to the resource).
  - Results are sorted by title for basic relevance.
- **Suggestions**: The `/api/search/suggestions?q=...` endpoint provides typeahead suggestions by searching model titles and returning unique matches.
- **Search Logging**: Queries from authenticated users are logged in the `search_logs` table. The `/api/search/logs` endpoint (admin-only) returns the top search terms.
- **Access Control**: The `EnsureUserIsAdmin` middleware restricts the logs endpoint to users with `is_admin=1`.

## Running Queues and Scheduler

### Queues

- **Purpose**: Indexing jobs are processed asynchronously using Laravel Queues with Redis.
- **Run Queue Worker**:
  ```bash
  docker-compose exec app php artisan queue:work
  ```
  For production, run in daemon mode:
  ```bash
  docker-compose exec app php artisan queue:work --daemon
  ```
- **Verify**: Ensure the Redis container is running (`docker-compose ps`) and check job processing:
  ```bash
  docker-compose exec redis redis-cli LLEN queues:default
  ```

### Scheduler

- **Purpose**: Automates periodic tasks, such as rebuilding the search index daily.
- **Setup**:
  - Edit `app/Console/Kernel.php` to schedule the rebuild command:
    ```php
    $schedule->command('scout:rebuild')->daily();
    ```
  - Set up a cron job to run the scheduler every minute:
    ```bash
    crontab -e
    ```
    Add:
    ```
    * * * * * cd /path/to/website-search-system && docker-compose exec app php artisan schedule:run >> /dev/null 2>&1
    ```
- **Run Manually** (for testing):
  ```bash
  docker-compose exec app php artisan schedule:run
  ```

## Sample Queries and Expected Results

1. **Search Query**:

   ```bash
   curl "http://localhost:8000/api/search?q=UT"
   ```

   **Expected Response**:

   ```json
   {
     "results": [
       {
         "type": "Product",
         "title": "voluptas",
         "snippet": "Aliquam eum magnam consequatur saepe nobis error consequuntur et. Alias et quo ex ducimus nisi. Aut ...",
         "link": "/product/6"
       },
       {
         "type": "Product",
         "title": "totam",
         "snippet": "Et doloremque doloremque cum quaerat placeat qui. Fugit laudantium doloribus nihil aliquid consequat...",
         "link": "/product/17"
       },
       {
         "type": "Product",
         "title": "rerum",
         "snippet": "Consequatur enim consequatur placeat repudiandae explicabo sit deleniti ullam. Quisquam libero repud...",
         "link": "/product/12"
       },
       {
         "type": "Product",
         "title": "repellendus",
         "snippet": "Ipsam quo ut aut maxime vel. Tenetur est ut et velit. Hic dolores eum pariatur minus commodi delectu...",
         "link": "/product/14"
       },
       {
         "type": "Product",
         "title": "ratione",
         "snippet": "Voluptas accusamus accusamus numquam commodi et velit voluptas. Sapiente officiis quis est aut assum...",
         "link": "/product/2"
       },
       {
         "type": "Product",
         "title": "provident",
         "snippet": "Dolor commodi hic qui dignissimos. Libero totam magnam delectus dolorem. Ut quaerat velit ab exceptu...",
         "link": "/product/15"
       }
     ],
     "pagination": {
       "total": 55,
       "per_page": 10,
       "current_page": 1,
       "last_page": 6
     }
   }
   ```

2. **Suggestions Query**:

   ```bash
   curl "http://localhost:8000/api/search/suggestions?q=UT"
   ```

   **Expected Response**:

   ```json
   {
     "0": "Consectetur ut laborum voluptatem in porro et ipsam.",
     "1": "Error et quisquam atque ipsam impedit recusandae.",
     "2": "Consequatur eos aut similique a voluptate soluta voluptatem debitis.",
     "3": "At ut vitae corporis quia est praesentium.",
     "4": "Quia quia ut tempora totam ipsum.",
     "5": null,
     "10": "Ea consequatur rem et aperiam temporibus.",
     "11": "Aliquam doloribus et sequi ut sit qui consequuntur quis.",
     "12": "Nisi suscipit assumenda error vel excepturi blanditiis reiciendis quam.",
     "13": "Commodi aperiam voluptate quisquam quia.",
     "14": "Aut libero eum alias dolorem."
   }
   ```

3. **Top Search Terms (Admin)**:
   - Requires authentication with a user having `is_admin=1`.
   - Create an admin user:
     ```bash
     docker-compose exec app php artisan tinker
     App\Models\User::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => bcrypt('password'), 'is_admin' => true]);
     ```
   - Log in via the frontend (`/login`) to get a token, then:
     ```bash
     curl -H "Authorization: Bearer <admin-token>" "http://localhost:8000/api/search/logs"
     ```
     **Expected Response**:
   ```json
   {
     "top_searches": ["UT", "product", "faq", "page"]
   }
   ```

## Additional Notes

- **Docker Setup**: The `Dockerfile` configures PHP 8.2 with MySQL support, and `docker-compose.yml` defines services for the Laravel app, MySQL, and Redis.
- **Postman Collection**: Import `postman_collection.json` to test API endpoints.
- **Artisan Commands**:
  - `php artisan scout:rebuild`: Rebuilds the search index.
  - `php artisan search:logs:clear`: Clears search logs.
  - `php artisan search:terms:top [--limit=10]`: Displays top search terms.
- **Frontend**: Uses Bootstrap 5 for a responsive search interface with typeahead suggestions.

```

```
