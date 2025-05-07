import { Router } from 'express';
import { validateCampaign } from '../middleware/validators';
import { authenticate } from '../middleware/auth';
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

router.use(authenticate);

router.post('/', validateCampaign, createCampaign);
router.get('/', getCampaigns);
router.get('/:id', getCampaign);
router.put('/:id', validateCampaign, updateCampaign);
router.delete('/:id', deleteCampaign);
router.post('/:id/start', startCampaign);
router.post('/:id/pause', pauseCampaign);
router.post('/:id/resume', resumeCampaign);
router.post('/:id/stop', stopCampaign);

export default router; 