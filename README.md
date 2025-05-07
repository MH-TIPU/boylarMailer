# Boylar Mailer - Email Marketing Platform

A modern email marketing platform built with React, TypeScript, and Node.js.

## Features

- User Authentication
- Email Template Management
- Campaign Management
- Subscriber List Management
- Campaign Analytics
- Email Scheduling
- Real-time Campaign Status Updates

## Tech Stack

### Frontend
- React
- TypeScript
- Material-UI
- React Router
- Axios
- Socket.IO Client

### Backend
- Node.js
- Express
- TypeScript
- TypeORM
- PostgreSQL
- Redis
- JWT Authentication
- Socket.IO
- Nodemailer

## Prerequisites

- Node.js (v14 or higher)
- PostgreSQL
- Redis
- SMTP Server (for sending emails)

## Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/boylar-mailer.git
cd boylar-mailer
```

2. Install dependencies:
```bash
# Install server dependencies
cd server
npm install

# Install client dependencies
cd ../client
npm install
```

3. Set up environment variables:
   - Copy `.env.example` to `.env` in both client and server directories
   - Update the variables with your configuration

4. Set up the database:
```bash
cd server
npm run typeorm migration:run
```

5. Start the development servers:
```bash
# Start the backend server
cd server
npm run dev

# Start the frontend server
cd ../client
npm start
```

## Project Structure

```
boylar-mailer/
├── client/                 # Frontend React application
│   ├── src/
│   │   ├── components/    # React components
│   │   ├── contexts/      # React contexts
│   │   ├── hooks/         # Custom hooks
│   │   ├── services/      # API services
│   │   └── types/         # TypeScript types
│   └── public/            # Static files
│
└── server/                # Backend Node.js application
    ├── src/
    │   ├── controllers/   # Route controllers
    │   ├── middleware/    # Custom middleware
    │   ├── models/        # Database models
    │   ├── routes/        # API routes
    │   └── services/      # Business logic
    └── migrations/        # Database migrations
```

## API Documentation

### Authentication

- `POST /api/auth/register` - Register a new user
- `POST /api/auth/login` - Login user
- `POST /api/auth/logout` - Logout user

### Templates

- `GET /api/templates` - Get all templates
- `POST /api/templates` - Create a new template
- `GET /api/templates/:id` - Get template by ID
- `PUT /api/templates/:id` - Update template
- `DELETE /api/templates/:id` - Delete template

### Campaigns

- `GET /api/campaigns` - Get all campaigns
- `POST /api/campaigns` - Create a new campaign
- `GET /api/campaigns/:id` - Get campaign by ID
- `PUT /api/campaigns/:id` - Update campaign
- `DELETE /api/campaigns/:id` - Delete campaign
- `POST /api/campaigns/:id/send` - Send campaign
- `POST /api/campaigns/:id/schedule` - Schedule campaign
- `POST /api/campaigns/:id/cancel` - Cancel scheduled campaign

### Subscriber Lists

- `GET /api/subscriber-lists` - Get all subscriber lists
- `POST /api/subscriber-lists` - Create a new subscriber list
- `GET /api/subscriber-lists/:id` - Get subscriber list by ID
- `PUT /api/subscriber-lists/:id` - Update subscriber list
- `DELETE /api/subscriber-lists/:id` - Delete subscriber list

### Subscribers

- `GET /api/subscriber-lists/:id/subscribers` - Get all subscribers in a list
- `POST /api/subscriber-lists/:id/subscribers` - Add subscriber to list
- `DELETE /api/subscriber-lists/:id/subscribers/:subscriberId` - Remove subscriber from list

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Support

For support, email support@boylarmailer.com or open an issue in the GitHub repository.
