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

    const handleSend = async () => {
        try {
            await axios.post(`/api/campaigns/${id}/send`);
            window.location.reload();
        } catch (err) {
            setError('Failed to send campaign');
            console.error('Error sending campaign:', err);
        }
    };

    const handleSchedule = async () => {
        const scheduledAt = prompt('Enter schedule date and time (YYYY-MM-DD HH:mm):');
        if (!scheduledAt) return;

        try {
            await axios.post(`/api/campaigns/${id}/schedule`, {
                scheduled_at: scheduledAt,
            });
            window.location.reload();
        } catch (err) {
            setError('Failed to schedule campaign');
            console.error('Error scheduling campaign:', err);
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
                    {campaign.status === 'draft' && (
                        <Box>
                            <Button
                                variant="contained"
                                startIcon={<SendIcon />}
                                onClick={handleSend}
                                sx={{ mr: 1 }}
                            >
                                Send Now
                            </Button>
                            <Button
                                variant="outlined"
                                startIcon={<ScheduleIcon />}
                                onClick={handleSchedule}
                            >
                                Schedule
                            </Button>
                        </Box>
                    )}
                </Box>

                <Paper sx={{ p: 3, mb: 3 }}>
                    <Typography variant="h4" gutterBottom>
                        {campaign.name}
                    </Typography>
                    <Typography variant="subtitle1" color="textSecondary" gutterBottom>
                        Subject: {campaign.subject}
                    </Typography>
                    <Typography variant="body2" color="textSecondary">
                        Template: {campaign.template.name}
                    </Typography>
                    <Typography variant="body2" color="textSecondary">
                        Subscriber List: {campaign.subscriber_list.name}
                    </Typography>
                    <Typography variant="body2" color="textSecondary">
                        Status: {campaign.status}
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
            </Box>
        </Container>
    );
};

export default CampaignShow; 