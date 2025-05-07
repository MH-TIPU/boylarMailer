import express from 'express';
import { auth } from '../middleware/auth';
import {
  createTemplate,
  getTemplates,
  getTemplate,
  updateTemplate,
  deleteTemplate,
} from '../controllers/templateController';

const router = express.Router();

// All routes require authentication
router.use(auth);

router.post('/', createTemplate);
router.get('/', getTemplates);
router.get('/:id', getTemplate);
router.put('/:id', updateTemplate);
router.delete('/:id', deleteTemplate);

export default router; 