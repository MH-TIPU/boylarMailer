import { Router } from 'express';
import { createSubscriberList, getSubscriberLists, getSubscriberList, updateSubscriberList, deleteSubscriberList, importSubscribers, exportSubscribers } from '../controllers/subscriberListController';
import { auth } from '../middleware/auth';
import { validateSubscriberList } from '../middleware/validators';
import multer from 'multer';

const router = Router();
const upload = multer({ dest: 'uploads/' });

// All routes require authentication
router.use(auth);

router.post('/', validateSubscriberList, createSubscriberList);
router.get('/', getSubscriberLists);
router.get('/:id', getSubscriberList);
router.put('/:id', validateSubscriberList, updateSubscriberList);
router.delete('/:id', deleteSubscriberList);

// Import/Export routes
router.post('/:id/import', upload.single('file'), importSubscribers);
router.get('/:id/export', exportSubscribers);

export default router; 