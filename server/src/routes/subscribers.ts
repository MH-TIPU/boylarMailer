import { Router } from 'express';
import { createSubscriber, getSubscribers, getSubscriber, updateSubscriber, deleteSubscriber, unsubscribe } from '../controllers/subscriberController';
import { auth } from '../middleware/auth';
import { validateSubscriber } from '../middleware/validators';

const router = Router();

// Public routes
router.post('/unsubscribe/:token', unsubscribe);

// Protected routes
router.use(auth);
router.post('/', validateSubscriber, createSubscriber);
router.get('/', getSubscribers);
router.get('/:id', getSubscriber);
router.put('/:id', validateSubscriber, updateSubscriber);
router.delete('/:id', deleteSubscriber);

export default router; 