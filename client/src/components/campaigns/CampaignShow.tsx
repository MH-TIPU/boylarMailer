import React, { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import {
    Box,
    Container,
    Typography,
    Paper,
    Grid,
    Button,
    CircularProgress,
    Card,
    CardContent,
    Divider,
    Chip
} from '@mui/material';
import {
    ArrowBack as ArrowBackIcon,
    Send as SendIcon,
    Schedule as ScheduleIcon,
} from '@mui/icons-material';
import axios from 'axios';

interface CampaignStats {
    total_sent: number;
    opened: number;
    clicked: number;
    bounced: number;
    unsubscribed: number;
}

interface Campaign {
    id: string;
    name: string;
    subject: string;
    content: string;
    status: string;
    scheduled_at?: string;
    sent_at?: string;
    template: {
        name: string;
    };
    subscriber_list: {
        name: string;
    };
    stats?: CampaignStats;
    _count: {
        emailsSent: number;
        emailsOpened: number;
        emailsClicked: number;
    };
}

const CampaignShow: React.FC = () => {
    const { id } = useParams<{ id: string }>();
    const navigate = useNavigate();
    const [campaign, setCampaign] = useState<Campaign | null>(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const fetchCampaign = async () => {
            try {
                const response = await axios.get(`/api/campaigns/${id}`);
                setCampaign(response.data);
            } catch (err) {
                setError('Failed to load campaign details');
                console.error('Error fetching campaign:', err);
            } finally {
                setLoading(false);
            }
        };

        fetchCampaign();
    }, [id]);

    const handleStart = async () => {
        try {
            await axios.post(`/api/campaigns/${id}/start`);
            navigate('/campaigns');
        } catch (error) {
            console.error('Error starting campaign:', error);
        }
    };

    const handlePause = async () => {
        try {
            await axios.post(`/api/campaigns/${id}/pause`);
            navigate('/campaigns');
        } catch (error) {
            console.error('Error pausing campaign:', error);
        }
    };

    const handleResume = async () => {
        try {
            await axios.post(`/api/campaigns/${id}/resume`);
            navigate('/campaigns');
        } catch (error) {
            console.error('Error resuming campaign:', error);
        }
    };

    const handleStop = async () => {
        try {
            await axios.post(`/api/campaigns/${id}/stop`);
            navigate('/campaigns');
        } catch (error) {
            console.error('Error stopping campaign:', error);
        }
    };

    if (loading) {
        return (
            <Box sx={{ display: 'flex', justifyContent: 'center', alignItems: 'center', minHeight: '60vh' }}>
                <CircularProgress />
            </Box>
        );
    }

    if (error || !campaign) {
        return (
            <Container maxWidth="md">
                <Box sx={{ mt: 4, textAlign: 'center' }}>
                    <Typography color="error" variant="h6">
                        {error || 'Campaign not found'}
                    </Typography>
                    <Button
                        startIcon={<ArrowBackIcon />}
                        onClick={() => navigate('/campaigns')}
                        sx={{ mt: 2 }}
                    >
                        Back to Campaigns
                    </Button>
                </Box>
            </Container>
        );
    }

    return (
        <Container maxWidth="lg">
            <Box sx={{ mt: 4, mb: 4 }}>
                <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', mb: 3 }}>
                    <Button
                        startIcon={<ArrowBackIcon />}
                        onClick={() => navigate('/campaigns')}
                    >
                        Back to Campaigns
                    </Button>
                    {campaign.status === 'DRAFT' && (
                        <Box>
                            <Button
                                variant="contained"
                                color="primary"
                                onClick={handleStart}
                                sx={{ mr: 1 }}
                            >
                                Start Campaign
                            </Button>
                        </Box>
                    )}
                    {['RUNNING', 'PAUSED'].includes(campaign.status) && (
                        <Box>
                            <Button
                                variant="contained"
                                color="warning"
                                onClick={handlePause}
                                sx={{ mr: 1 }}
                            >
                                Pause Campaign
                            </Button>
                            <Button
                                variant="contained"
                                color="success"
                                onClick={handleResume}
                                sx={{ mr: 1 }}
                            >
                                Resume Campaign
                            </Button>
                            <Button
                                variant="contained"
                                color="error"
                                onClick={handleStop}
                            >
                                Stop Campaign
                            </Button>
                        </Box>
                    )}
                </Box>

                <Paper sx={{ p: 3, mb: 3 }}>
                    <Typography variant="h4" gutterBottom>
                        {campaign.name}
                    </Typography>
                    <Chip
                        label={campaign.status}
                        color={
                            campaign.status === 'RUNNING'
                                ? 'success'
                                : campaign.status === 'PAUSED'
                                ? 'warning'
                                : 'default'
                        }
                        sx={{ mb: 2 }}
                    />
                    <Typography variant="subtitle1" color="textSecondary" gutterBottom>
                        Subject: {campaign.subject}
                    </Typography>
                    <Typography variant="body2" color="textSecondary">
                        Template: {campaign.template.name}
                    </Typography>
                    <Typography variant="body2" color="textSecondary">
                        Subscriber List: {campaign.subscriber_list.name}
                    </Typography>
                    {campaign.scheduled_at && (
                        <Typography variant="body2" color="textSecondary">
                            Scheduled for: {new Date(campaign.scheduled_at).toLocaleString()}
                        </Typography>
                    )}
                    {campaign.sent_at && (
                        <Typography variant="body2" color="textSecondary">
                            Sent at: {new Date(campaign.sent_at).toLocaleString()}
                        </Typography>
                    )}
                </Paper>

                {campaign.stats && (
                    <Box sx={{ 
                        display: 'grid', 
                        gridTemplateColumns: {
                            xs: '1fr',
                            sm: 'repeat(2, 1fr)',
                            md: 'repeat(5, 1fr)'
                        },
                        gap: 3,
                        mb: 3
                    }}>
                        <Card>
                            <CardContent>
                                <Typography variant="h6" align="center">
                                    {campaign.stats.total_sent}
                                </Typography>
                                <Typography variant="body2" color="textSecondary" align="center">
                                    Total Sent
                                </Typography>
                            </CardContent>
                        </Card>
                        <Card>
                            <CardContent>
                                <Typography variant="h6" align="center">
                                    {campaign.stats.opened}
                                </Typography>
                                <Typography variant="body2" color="textSecondary" align="center">
                                    Opened
                                </Typography>
                            </CardContent>
                        </Card>
                        <Card>
                            <CardContent>
                                <Typography variant="h6" align="center">
                                    {campaign.stats.clicked}
                                </Typography>
                                <Typography variant="body2" color="textSecondary" align="center">
                                    Clicked
                                </Typography>
                            </CardContent>
                        </Card>
                        <Card>
                            <CardContent>
                                <Typography variant="h6" align="center">
                                    {campaign.stats.bounced}
                                </Typography>
                                <Typography variant="body2" color="textSecondary" align="center">
                                    Bounced
                                </Typography>
                            </CardContent>
                        </Card>
                        <Card>
                            <CardContent>
                                <Typography variant="h6" align="center">
                                    {campaign.stats.unsubscribed}
                                </Typography>
                                <Typography variant="body2" color="textSecondary" align="center">
                                    Unsubscribed
                                </Typography>
                            </CardContent>
                        </Card>
                    </Box>
                )}

                <Paper sx={{ p: 3 }}>
                    <Typography variant="h6" gutterBottom>
                        Email Content
                    </Typography>
                    <Divider sx={{ mb: 2 }} />
                    <Box
                        sx={{
                            backgroundColor: '#f5f5f5',
                            p: 2,
                            borderRadius: 1,
                            whiteSpace: 'pre-wrap',
                        }}
                    >
                        {campaign.content}
                    </Box>
                </Paper>

                <Paper sx={{ p: 3 }}>
                    <Typography variant="h6" gutterBottom>
                        Statistics
                    </Typography>
                    <Grid container spacing={2}>
                        <Grid item xs={4}>
                            <Paper sx={{ p: 2, textAlign: 'center' }}>
                                <Typography variant="h6">{campaign._count.emailsSent}</Typography>
                                <Typography variant="body2">Emails Sent</Typography>
                            </Paper>
                        </Grid>
                        <Grid item xs={4}>
                            <Paper sx={{ p: 2, textAlign: 'center' }}>
                                <Typography variant="h6">{campaign._count.emailsOpened}</Typography>
                                <Typography variant="body2">Opens</Typography>
                            </Paper>
                        </Grid>
                        <Grid item xs={4}>
                            <Paper sx={{ p: 2, textAlign: 'center' }}>
                                <Typography variant="h6">{campaign._count.emailsClicked}</Typography>
                                <Typography variant="body2">Clicks</Typography>
                            </Paper>
                        </Grid>
                    </Grid>
                </Paper>
            </Box>
        </Container>
    );
};

export default CampaignShow; 