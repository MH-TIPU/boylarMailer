import { Router } from 'express';
import { validateCampaign } from '../middleware/validators';
import { auth } from '../middleware/auth';
import { 
    createCampaign,
    getCampaigns,
    getCampaign,
    updateCampaign,
    deleteCampaign,
    startCampaign,
    pauseCampaign,
    resumeCampaign,
    stopCampaign
} from '../controllers/campaignController';

const router = Router();

// All routes require authentication
router.use(auth);

router.post('/', validateCampaign, createCampaign);
router.get('/', getCampaigns);
router.get('/:id', getCampaign);
router.put('/:id', validateCampaign, updateCampaign);
router.delete('/:id', deleteCampaign);

// Campaign control routes
router.post('/:id/start', startCampaign);
router.post('/:id/pause', pauseCampaign);
router.post('/:id/resume', resumeCampaign);
router.post('/:id/stop', stopCampaign);

export default router; 