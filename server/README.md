# Boylar Mailer Server

A robust email marketing platform server built with Node.js, Express, and TypeScript.

## Features

- User authentication and authorization
- Email campaign management
- Subscriber list management
- Email template management
- Real-time campaign metrics
- Rate limiting and security features
- Monitoring and logging
- Queue-based email sending

## Prerequisites

- Node.js (v18 or higher)
- PostgreSQL
- Redis
- SMTP server

## Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/boylar-mailer.git
cd boylar-mailer/server
```

2. Install dependencies:
```bash
npm install
```

3. Copy the environment file and update the variables:
```bash
cp env.example .env
```

4. Set up the database:
```bash
npx prisma migrate dev
```

## Development

Start the development server:
```bash
npm run dev
```

## Building

Build the project:
```bash
npm run build
```

## Running

Start the production server:
```bash
npm start
```

## Testing

Run tests:
```bash
npm test
```

## API Documentation

The API documentation is available at `/api-docs` when running the server.

## Environment Variables

- `PORT`: Server port (default: 3000)
- `NODE_ENV`: Environment (development/production)
- `DATABASE_URL`: PostgreSQL connection string
- `JWT_SECRET`: Secret for JWT tokens
- `JWT_EXPIRES_IN`: JWT token expiration time
- `REDIS_HOST`: Redis host
- `REDIS_PORT`: Redis port
- `REDIS_PASSWORD`: Redis password
- `SMTP_HOST`: SMTP server host
- `SMTP_PORT`: SMTP server port
- `SMTP_USER`: SMTP username
- `SMTP_PASS`: SMTP password
- `SMTP_FROM`: Default sender email
- `RATE_LIMIT_WINDOW_MS`: Rate limit window in milliseconds
- `RATE_LIMIT_MAX`: Maximum requests per window
- `LOG_LEVEL`: Logging level

## License

MIT 