import { Router } from 'express';
import { createTemplate, getTemplates, getTemplate, updateTemplate, deleteTemplate } from '../controllers/templateController';
import { auth } from '../middleware/auth';
import { validateTemplate } from '../middleware/validators';

const router = Router();

// All routes require authentication
router.use(auth);

router.post('/', validateTemplate, createTemplate);
router.get('/', getTemplates);
router.get('/:id', getTemplate);
router.put('/:id', validateTemplate, updateTemplate);
router.delete('/:id', deleteTemplate);

export default router; 