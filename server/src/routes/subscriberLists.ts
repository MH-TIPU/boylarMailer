import { Router } from 'express';
import { createSubscriberList, getSubscriberLists, getSubscriberList, updateSubscriberList, deleteSubscriberList, importSubscribers, exportSubscribers } from '../controllers/subscriberListController';
import { auth } from '../middleware/auth';
import { validateSubscriberList } from '../middleware/validators';
import multer from 'multer';
import { Request, Response } from 'express';

const router = Router();

// Configure multer for file uploads
const storage = multer.memoryStorage();
const upload = multer({
    storage,
    limits: {
        fileSize: 5 * 1024 * 1024 // 5MB limit
    },
    fileFilter: (req, file, cb) => {
        if (file.mimetype === 'text/csv') {
            cb(null, true);
        } else {
            cb(new Error('Only CSV files are allowed'));
        }
    }
});

// All routes require authentication
router.use(auth);

router.post('/', validateSubscriberList, createSubscriberList);
router.get('/', getSubscriberLists);
router.get('/:id', getSubscriberList);
router.put('/:id', validateSubscriberList, updateSubscriberList);
router.delete('/:id', deleteSubscriberList);

// Import/Export routes
router.post('/:id/import', upload.single('file'), async (req: Request, res: Response) => {
    if (!req.file) {
        return res.status(400).json({ message: 'No file uploaded' });
    }
    return importSubscribers(req as any, res);
});

router.get('/:id/export', exportSubscribers);

export default router; 