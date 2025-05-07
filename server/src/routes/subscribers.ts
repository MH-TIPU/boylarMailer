import { Router } from 'express';
import { validateSubscriber } from '../middleware/validators';
import { authenticate } from '../middleware/auth';
import {
    createSubscriber,
    getSubscribers,
    getSubscriber,
    updateSubscriber,
    deleteSubscriber,
    unsubscribe
} from '../controllers/subscriberController';

const router = Router();

router.use(authenticate);

router.post('/', validateSubscriber, createSubscriber);
router.get('/', getSubscribers);
router.get('/:id', getSubscriber);
router.put('/:id', validateSubscriber, updateSubscriber);
router.delete('/:id', deleteSubscriber);
router.post('/:id/unsubscribe', unsubscribe);

export default router; 