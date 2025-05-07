import { Router } from 'express';
import { validateSubscriberList } from '../middleware/validators';
import { authenticate } from '../middleware/auth';
import {
    createSubscriberList,
    getSubscriberLists,
    getSubscriberList,
    updateSubscriberList,
    deleteSubscriberList,
    importSubscribers,
    exportSubscribers
} from '../controllers/subscriberListController';

const router = Router();

router.use(authenticate);

router.post('/', validateSubscriberList, createSubscriberList);
router.get('/', getSubscriberLists);
router.get('/:id', getSubscriberList);
router.put('/:id', validateSubscriberList, updateSubscriberList);
router.delete('/:id', deleteSubscriberList);
router.post('/:id/import', importSubscribers);
router.get('/:id/export', exportSubscribers);

export default router; 