# Pusher Configuration for Local Testing

## Option 1: Use Pusher.com (Recommended)

1. Go to [pusher.com](https://pusher.com) and create a free account
2. Create a new app
3. Get your credentials:
   - App ID
   - Key
   - Secret
   - Cluster (usually 'mt1')

4. Add to your `.env` file:
```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=mt1

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

## Option 2: Use Laravel WebSockets (Alternative)

If you prefer not to use Pusher.com, you can use Laravel WebSockets:

1. Install Laravel WebSockets:
```bash
composer require beyondcode/laravel-websockets
php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="migrations"
php artisan migrate
```

2. Update your `.env`:
```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=local
PUSHER_APP_KEY=local
PUSHER_APP_SECRET=local
PUSHER_HOST=127.0.0.1
PUSHER_PORT=6001
PUSHER_SCHEME=http

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

3. Start the WebSocket server:
```bash
php artisan websockets:serve
```

## Testing Without Real-time (Fallback)

If you want to test the basic functionality without real-time features:

1. Set broadcast driver to log:
```env
BROADCAST_DRIVER=log
```

2. Messages will be logged to `storage/logs/laravel.log` instead of being broadcast
3. You can still test sending/receiving messages via API calls

## Quick Test Setup

For immediate testing, you can use these demo Pusher credentials (replace with your own):

```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=123456
PUSHER_APP_KEY=abcdef123456
PUSHER_APP_SECRET=secret123456
PUSHER_APP_CLUSTER=mt1

VITE_PUSHER_APP_KEY=abcdef123456
VITE_PUSHER_HOST=
VITE_PUSHER_PORT=443
VITE_PUSHER_SCHEME=https
VITE_PUSHER_APP_CLUSTER=mt1
```

**Note:** These are demo credentials and won't work for real-time features. Get your own from pusher.com for full functionality.
