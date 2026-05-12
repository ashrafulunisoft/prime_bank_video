# Laravel Video Calling Platform

A scalable real-time communication platform built with <a href="https://laravel.com">Laravel</a>, <a href="https://reverb.laravel.com">Laravel Reverb</a>, <a href="https://laravel.com/docs/octane">Laravel Octane</a>, <a href="https://roadrunner.dev">RoadRunner</a>, Docker, NGINX, MySQL, Redis, and GitHub Actions CI/CD.

---

# 🚀 Features

* 🎥 One-to-One & Group Video Calling
* 📺 Screen Sharing
* 📁 Real-Time File Sharing
* 💬 Live Chat Messaging
* 🔔 Real-Time Notifications using Laravel Reverb
* 🟢 Online / Offline Presence Detection
* ⚡ High Performance with Laravel Octane + RoadRunner
* 🔄 Redis Queue & Cache
* 🐳 Dockerized Infrastructure
* 🔐 Authentication & Authorization
* 🚀 CI/CD with GitHub Actions
* 🌐 NGINX Reverse Proxy
* 📊 Scalable Architecture

---

# 🏗️ Tech Stack

| Layer            | Technology                  |
| ---------------- | --------------------------- |
| Backend          | PHP 8.3, Laravel            |
| Realtime         | Laravel Reverb              |
| Video Calling    | WebRTC                      |
| Performance      | Laravel Octane + RoadRunner |
| Database         | MySQL 8                     |
| Cache & Queue    | Redis                       |
| Reverse Proxy    | NGINX                       |
| Containerization | Docker & Docker Compose     |
| CI/CD            | GitHub Actions              |
| Frontend         | React / Vue / Next.js       |
| Database Tool    | phpMyAdmin                  |

---

# 📦 System Architecture

```text id="v2t6j9"
Users
  │
  ▼
NGINX Reverse Proxy
  │
  ▼
Laravel Octane + RoadRunner
  │
 ├──────────────┐
 │              │
 ▼              ▼
Laravel Reverb  Redis
 │              │
 ▼              ▼
Realtime WS     Queue / Cache

        ▼
      MySQL
```

---

# 📁 Project Structure

```bash id="j0v81r"
project-root/
│
├── app/
├── bootstrap/
├── config/
├── database/
├── docker/
│   ├── nginx/
│   ├── mysql/
│   ├── redis/
│   └── phpmyadmin/
│
├── routes/
├── resources/
├── storage/
├── .github/
│   └── workflows/
│       └── deploy.yml
│
├── docker-compose.yml
├── Dockerfile
└── README.md
```

---

# ⚙️ Requirements

* PHP 8.3+
* Composer
* Node.js 20+
* Docker
* Docker Compose

---

# 🐳 Docker Services

| Service        | Port |
| -------------- | ---- |
| Laravel App    | 8000 |
| NGINX          | 80   |
| MySQL          | 3306 |
| Redis          | 6379 |
| phpMyAdmin     | 8081 |
| Laravel Reverb | 8080 |

---

# 🐋 Docker Compose Setup

## Start Containers

```bash id="7lmp0j"
docker compose up -d
```

---

## Stop Containers

```bash id="9k7xj4"
docker compose down
```

---

## Rebuild Containers

```bash id="m4tx8u"
docker compose up -d --build
```

---

# 🔧 Local Laravel Setup

## Install Dependencies

```bash id="7yo6np"
composer install
npm install
```

---

## Copy Environment File

```bash id="0u8n4a"
cp .env.example .env
```

---

## Generate Application Key

```bash id="5w8n0y"
php artisan key:generate
```

---

# 🐬 MySQL Configuration

```env id="v0n2ms"
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=video_calling
DB_USERNAME=root
DB_PASSWORD=secret
```

---

# 🔴 Redis Configuration

```env id="l2a0tp"
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379
```

---

# ⚡ Laravel Octane Setup

## Install Octane

```bash id="6a7z0x"
composer require laravel/octane
```

---

## Install RoadRunner

```bash id="0x9m2w"
composer require spiral/roadrunner-cli spiral/roadrunner-http
```

---

## Install Octane Server

```bash id="5e8v9c"
php artisan octane:install --server=roadrunner
```

---

# ▶️ Run Laravel Octane

```bash id="8u6s3v"
php artisan octane:start --server=roadrunner --host=0.0.0.0 --port=8000
```

---

# 📡 Laravel Reverb Setup

## Install Reverb

```bash id="9z0q2v"
composer require laravel/reverb
```

---

## Configure Reverb

```bash id="3j7f8d"
php artisan reverb:install
```

---

## Run Reverb Server

```bash id="8c5t2u"
php artisan reverb:start
```

---

# 🔔 Broadcasting Configuration

```env id="3r8m5x"
BROADCAST_CONNECTION=reverb

REVERB_APP_ID=video-app
REVERB_APP_KEY=video-key
REVERB_APP_SECRET=video-secret

REVERB_HOST=0.0.0.0
REVERB_PORT=8080
REVERB_SCHEME=http
```

---

# 🌐 NGINX Configuration

Example NGINX Reverse Proxy:

```nginx id="0f2l8m"
server {
    listen 80;

    server_name localhost;

    location / {
        proxy_pass http://app:8000;

        proxy_set_header Host $host;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }

    location /app {
        proxy_pass http://reverb:8080;

        proxy_http_version 1.1;

        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "Upgrade";

        proxy_set_header Host $host;
    }
}
```

---

# 📞 Video Calling (WebRTC)

## Features

* Camera & microphone streaming
* Peer-to-peer communication
* Screen sharing
* STUN/TURN support
* Live connection state monitoring

---

# 📁 File Sharing Features

* Drag & drop uploads
* Real-time upload progress
* Secure file validation
* Chunk uploads for large files
* Signed download URLs

---

# 🔔 Real-Time Notifications

Powered by:

* Laravel Reverb
* Redis Pub/Sub
* Laravel Echo

Examples:

* Incoming call alerts
* User online/offline
* New message notifications
* File upload completion

---

# 🧠 Queue Worker

## Run Queue Worker

```bash id="2h4v9q"
php artisan queue:work
```

---

## Queue Driver

```env id="9m3t0r"
QUEUE_CONNECTION=redis
```

---

# 🔐 Authentication

Recommended:

* Laravel Sanctum
* JWT Authentication
* OAuth2 (Optional)

---

# 🗄️ phpMyAdmin Access

| Field    | Value                                          |
| -------- | ---------------------------------------------- |
| URL      | [http://localhost:8081](http://localhost:8081) |
| Username | root                                           |
| Password | secret                                         |

---

# 🚀 GitHub Actions CI/CD

## Workflow Features

* Install Dependencies
* Run Tests
* Build Docker Image
* Push Docker Image
* Deploy to Server

---

# 📁 GitHub Actions Workflow

File:

```text id="x1r7s9"
.github/workflows/deploy.yml
```

Example Workflow:

```yaml id="5p9z1w"
name: Deploy Application

on:
  push:
    branches:
      - main

jobs:
  deploy:
    runs-on: ubuntu-latest

    steps:
      - name: Checkout Repository
        uses: actions/checkout@v4

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.3

      - name: Install Dependencies
        run: composer install --no-dev --optimize-autoloader

      - name: Run Tests
        run: php artisan test

      - name: Build Docker Image
        run: docker build -t video-calling-app .

      - name: Deploy Server
        run: echo "Deploy Process Here"
```

---

# 🧪 Testing

## Backend Tests

```bash id="0v5y8m"
php artisan test
```

---

## Frontend Tests

```bash id="2r7m1x"
npm run test
```

---

# 🔥 Performance Optimization

* Laravel Octane Workers
* RoadRunner Persistent Workers
* Redis Cache
* Database Indexing
* NGINX Reverse Proxy
* Queue Offloading
* Lazy Loading Optimization

---

# 🛡️ Security Best Practices

* HTTPS / WSS
* Rate Limiting
* CSRF Protection
* File Validation
* Secure WebSocket Authentication
* TURN Authentication
* Encrypted Environment Variables

---

# 👨‍💻 Development Commands

## Run Vite

```bash id="1j4m9n"
npm run dev
```

---

## Run Queue Worker

```bash id="9t5v0u"
php artisan queue:work
```

---

## Run Scheduler

```bash id="3m6p8r"
php artisan schedule:work
```

---

## Run Reverb

```bash id="7k0w2x"
php artisan reverb:start
```

---

## Run Octane

```bash id="6q4x9z"
php artisan octane:start --server=roadrunner
```

---

# 📈 Future Improvements

* Mobile Applications
* Push Notifications
* AI Noise Cancellation
* Meeting Recording
* Live Streaming
* End-to-End Encryption
* Multi-device Sync

---

# 📊 Realtime Communication Flow

```text id="1q8v4z"
User A Starts Call
        │
        ▼
Laravel API Authentication
        │
        ▼
Reverb Broadcast Event
        │
        ▼
User B Receives Notification
        │
        ▼
WebRTC Peer Connection
        │
        ▼
Video / Audio / Screen Stream
```

---

# 🤝 Contributing

1. Fork Repository
2. Create Feature Branch
3. Commit Changes
4. Push Branch
5. Open Pull Request

---

# 📜 License

MIT License

---

# 🙌 Credits

Built with:

* Laravel
* Laravel Reverb
* Laravel Octane
* RoadRunner
* Redis
* MySQL
* Docker
* GitHub Actions
* NGINX
* WebRTC
