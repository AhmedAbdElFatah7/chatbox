# Real-time Chat System Setup Guide

## Overview
This Laravel application now includes a complete real-time chat system using Pusher for broadcasting messages.

## Features Implemented

### Backend (Laravel)
1. **Message Model & Migration** - Stores chat messages with sender/receiver relationships
2. **ChatController** - Handles all chat operations:
   - Send messages
   - Get conversations
   - Mark messages as read
   - Get unread count
   - Get recent conversations
3. **MessageSent Event** - Broadcasts messages in real-time using Pusher
4. **API Routes** - RESTful endpoints for chat functionality
5. **Broadcasting Configuration** - Pusher integration setup

### Frontend (JavaScript)
1. **Real-time Chat Interface** - Modern, responsive chat UI
2. **Pusher Integration** - Listens for real-time message updates
3. **Conversation Management** - Shows recent conversations and unread counts
4. **Message Display** - Real-time message rendering with timestamps

## Setup Instructions

### 1. Database Configuration
Create a `.env` file in your project root with the following database settings:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=chat_box
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 2. Pusher Configuration
Add your Pusher credentials to the `.env` file:

```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=mt1
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

### 3. Run Migrations
```bash
php artisan migrate
```

### 4. Install Frontend Dependencies
```bash
npm install
```

### 5. Build Frontend Assets
```bash
npm run dev
# or for production
npm run build
```

## API Endpoints

All endpoints require authentication via JWT token.

### Chat Endpoints
- `POST /api/chat/send` - Send a message
- `GET /api/chat/conversation/{userId}` - Get conversation with a user
- `POST /api/chat/mark-read/{userId}` - Mark messages as read
- `GET /api/chat/unread-count` - Get unread messages count
- `GET /api/chat/conversations` - Get recent conversations

### Web Routes
- `GET /chat` - Chat interface (requires web authentication)

## Usage

### Sending Messages
```javascript
// Send a message
const response = await axios.post('/api/chat/send', {
    receiver_id: 2,
    message: 'Hello!'
});
```

### Real-time Updates
The system automatically listens for new messages using Pusher:

```javascript
// Messages are received in real-time
window.Echo.private(`chat.${userId}`)
    .listen('MessageSent', (data) => {
        // Handle new message
        console.log('New message:', data);
    });
```

## Security Features

1. **Friend-only Messaging** - Users can only send messages to their friends
2. **Authentication Required** - All endpoints require valid JWT tokens
3. **Private Channels** - Messages are broadcast only to sender and receiver
4. **Input Validation** - Message content is validated and sanitized

## File Structure

```
app/
├── Http/Controllers/Chat/ChatController.php
├── Models/Message.php
├── Events/MessageSent.php
├── Traits/ApiResponse.php

database/migrations/
└── 2025_10_11_123933_create_messages_table.php

resources/
├── views/chat.blade.php
├── js/bootstrap.js (updated with Pusher)
└── js/app.js

routes/
├── api.php (updated with chat routes)
├── web.php (updated with chat page)
└── channels.php (updated with chat channels)
```

## Next Steps

1. Set up your Pusher account and get credentials
2. Configure your database connection
3. Run the migrations
4. Test the chat functionality
5. Customize the UI as needed

The chat system is now ready to use! Users can send real-time messages to their friends, and the interface will update instantly using Pusher broadcasting.

