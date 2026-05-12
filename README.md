# Laravel Video Calling Platform

A scalable real-time communication platform built with <a href="https://laravel.com">Laravel</a>, <a href="https://reverb.laravel.com">Laravel Reverb</a>, <a href="https://laravel.com/docs/octane">Laravel Octane</a>, <a href="https://roadrunner.dev">RoadRunner</a>, Docker, PostgreSQL, Redis, and GitOps deployment using <a href="https://argo-cd.readthedocs.io">ArgoCD</a>.

---

## 🚀 Features

* 🎥 One-to-One & Group Video Calling
* 📺 Screen Sharing
* 📁 Real-Time File Sharing
* 🔔 Real-Time Notifications using Laravel Reverb
* 💬 Live Messaging
* 🟢 User Presence Detection
* ⚡ High Performance with Laravel Octane + RoadRunner
* 🧠 Redis Queue & Broadcasting
* 🐘 PostgreSQL Database
* 🐳 Dockerized Infrastructure
* ☸️ GitOps Deployment using ArgoCD + Kubernetes
* 🔐 Authentication & Authorization
* 📊 Scalable Architecture

---

# 🏗️ Tech Stack

| Layer            | Technology                       |
| ---------------- | -------------------------------- |
| Backend          | PHP 8.3, Laravel                 |
| Realtime         | Laravel Reverb, WebSockets       |
| Video Call       | WebRTC                           |
| Performance      | Laravel Octane + RoadRunner      |
| Queue & Cache    | Redis                            |
| Database         | PostgreSQL                       |
| Containerization | Docker & Docker Compose          |
| CI/CD            | GitHub Actions                   |
| GitOps           | ArgoCD                           |
| Reverse Proxy    | NGINX                            |
| Admin DB Tool    | pgAdmin                          |
| Frontend         | React / Next.js / Vue (Optional) |

---

# 📦 System Architecture

```text
Client Apps
    │
    ▼
NGINX Reverse Proxy
    │
    ▼
Laravel Octane (RoadRunner)
    │
 ┌──┼───────────────┐
 │  │               │
 ▼  ▼               ▼
Reverb           Redis Queue
 │                  │
 ▼                  ▼
WebSocket       Notifications
Broadcasting    Cache / Queue

    ▼
PostgreSQL Database
```

---

# 📁 Project Structure

```bash
project-root/
│
├── app/
├── bootstrap/
├── config/
├── database/
├── docker/
│   ├── nginx/
│   ├── postgres/
│   ├── redis/
│   └── pgadmin/
│
├── kubernetes/
│   ├── deployment.yaml
│   ├── service.yaml
│   ├── ingress.yaml
│   └── argocd-app.yaml
│
├── routes/
├── storage/
├── resources/
├── docker-compose.yml
├── Dockerfile
└── README.md
```

---

# ⚙️ Requirements

* PHP 8.3+
* Composer
* Docker
* Docker Compose
* Node.js 20+
* PostgreSQL
* Redis

---

# 🐳 Docker Services

The project includes the following services:

| Service     | Port |
| ----------- | ---- |
| Laravel App | 8000 |
| PostgreSQL  | 5432 |
| Redis       | 6379 |
| pgAdmin     | 5050 |
| Reverb      | 8080 |

---

# 🐋 Docker Compose Setup

## Start Containers

```bash
docker compose up -d
```

## Stop Containers

```bash
docker compose down
```

## Rebuild Containers

```bash
docker compose up -d --build
```

---

# 🔧 Local Laravel Setup

## Install Dependencies

```bash
composer install
npm install
```

---

## Copy Environment File

```bash
cp .env.example .env
```

---

## Generate App Key

```bash
php artisan key:generate
```

---

# 🐘 PostgreSQL Configuration

```env
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=video_calling
DB_USERNAME=postgres
DB_PASSWORD=secret
```

---

# 🔴 Redis Configuration

```env
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379
```

---

# ⚡ Laravel Octane Setup

## Install Octane

```bash
composer require laravel/octane
```

## Install RoadRunner

```bash
composer require spiral/roadrunner-cli spiral/roadrunner-http
```

## Install Octane Server

```bash
php artisan octane:install --server=roadrunner
```

---

# ▶️ Run Laravel Octane

```bash
php artisan octane:start --server=roadrunner --host=0.0.0.0 --port=8000
```

---

# 📡 Laravel Reverb Setup

## Install Reverb

```bash
composer require laravel/reverb
```

## Install Reverb

```bash
php artisan reverb:install
```

---

# ▶️ Run Reverb Server

```bash
php artisan reverb:start
```

---

# 🔔 Broadcasting Configuration

```env
BROADCAST_CONNECTION=reverb

REVERB_APP_ID=app-id
REVERB_APP_KEY=app-key
REVERB_APP_SECRET=app-secret

REVERB_HOST=0.0.0.0
REVERB_PORT=8080
REVERB_SCHEME=http
```

---

# 📞 WebRTC Video Calling

## Features

* Peer-to-peer communication
* STUN/TURN server support
* Camera & microphone streaming
* Screen sharing
* Call recording support (optional)

## Suggested Libraries

| Purpose         | Library      |
| --------------- | ------------ |
| WebRTC Helper   | SimplePeer   |
| Media Handling  | WebRTC APIs  |
| Realtime Events | Laravel Echo |

---

# 📁 File Sharing

## Supported Features

* Real-time upload progress
* Drag & drop upload
* Secure signed URLs
* Chunk uploads for large files

## Recommended Storage

* Local Storage
* AWS S3
* MinIO

---

# 🔔 Real-Time Notifications

Powered by:

* Laravel Reverb
* Laravel Echo
* Redis Pub/Sub

Examples:

* Incoming call notification
* User online/offline
* File upload complete
* New message alerts

---

# 🧠 Queue Workers

Run Queue Worker:

```bash
php artisan queue:work
```

Recommended Queue Driver:

```env
QUEUE_CONNECTION=redis
```

---

# 🛡️ Authentication

Recommended:

* Laravel Sanctum
* JWT Authentication
* OAuth2 (optional)

---

# 📊 pgAdmin Access

| Field    | Value                                          |
| -------- | ---------------------------------------------- |
| URL      | [http://localhost:5050](http://localhost:5050) |
| Email    | [admin@example.com](mailto:admin@example.com)  |
| Password | admin                                          |

---

# ☸️ Kubernetes Deployment

## Apply Kubernetes Resources

```bash
kubectl apply -f kubernetes/
```

---

# 🚀 ArgoCD GitOps Deployment

## Create ArgoCD Application

```bash
kubectl apply -f kubernetes/argocd-app.yaml
```

## Sync Application

```bash
argocd app sync video-calling-app
```

---

# 🔄 CI/CD Pipeline

Example Flow:

```text
Developer Push
      │
      ▼
GitHub Actions
      │
      ▼
Docker Build & Push
      │
      ▼
Kubernetes Manifest Update
      │
      ▼
ArgoCD Auto Sync
      │
      ▼
Production Deployment
```

---

# 🔥 Performance Optimizations

* Laravel Octane
* RoadRunner Worker Pool
* Redis Cache
* Database Indexing
* Queue Offloading
* WebSocket Scaling
* CDN for File Delivery

---

# 🧪 Testing

## Run Backend Tests

```bash
php artisan test
```

## Run Frontend Tests

```bash
npm run test
```

---

# 🔐 Security Best Practices

* HTTPS/WSS
* Signed Upload URLs
* Rate Limiting
* CSRF Protection
* WebRTC TURN Authentication
* File Validation
* Encrypted Storage

---

# 📈 Future Improvements

* AI Noise Cancellation
* Meeting Recording
* Multi-device Sync
* Push Notifications
* E2E Encryption
* Mobile Apps
* Live Streaming

---

# 👨‍💻 Development Commands

## Run Vite

```bash
npm run dev
```

## Run Queue

```bash
php artisan queue:work
```

## Run Scheduler

```bash
php artisan schedule:work
```

## Run Reverb

```bash
php artisan reverb:start
```

## Run Octane

```bash
php artisan octane:start --server=roadrunner
```

---

# 📜 License

MIT License

---

# 🤝 Contributing

1. Fork the repository
2. Create feature branch
3. Commit changes
4. Push branch
5. Open Pull Request

---

# ⭐ Recommended Production Stack

| Component      | Recommendation       |
| -------------- | -------------------- |
| Reverse Proxy  | NGINX                |
| SSL            | Let's Encrypt        |
| Monitoring     | Prometheus + Grafana |
| Logs           | Loki                 |
| Object Storage | MinIO / S3           |
| TURN Server    | Coturn               |
| Kubernetes     | K3s / EKS / AKS      |

---

# 📞 Core Realtime Flow

```text
User A Initiates Call
        │
        ▼
Laravel API Authenticates
        │
        ▼
Reverb Broadcasts Event
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

# 🙌 Credits

Built with:

* Laravel
* Laravel Reverb
* Laravel Octane
* RoadRunner
* PostgreSQL
* Redis
* Docker
* ArgoCD
* Kubernetes
* WebRTC
