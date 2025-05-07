import { Router } from 'express';
import { register, login, requestPasswordReset, resetPassword, getCurrentUser } from '../controllers/authController';
import { auth } from '../middleware/auth';

const router = Router();

// Public routes
router.post('/register', register);
router.post('/login', login);
router.post('/request-reset', requestPasswordReset);
router.post('/reset-password', resetPassword);

// Protected routes
router.get('/me', auth, getCurrentUser);

export default router; 