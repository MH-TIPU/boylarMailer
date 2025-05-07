import { Router } from 'express';
import authRoutes from './auth';
import templateRoutes from './templates';
import campaignRoutes from './campaigns';
import subscriberListRoutes from './subscriberLists';
import subscriberRoutes from './subscribers';

const router = Router();

router.use('/auth', authRoutes);
router.use('/templates', templateRoutes);
router.use('/campaigns', campaignRoutes);
router.use('/subscriber-lists', subscriberListRoutes);
router.use('/subscribers', subscriberRoutes);

export default router; 