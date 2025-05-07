import React, { useState, useEffect } from 'react';
import {
    Box,
    Button,
    Card,
    CardContent,
    Typography,
    IconButton,
    Dialog,
    DialogTitle,
    DialogContent,
    DialogActions,
    TextField,
    Table,
    TableBody,
    TableCell,
    TableContainer,
    TableHead,
    TableRow,
    Paper,
    Chip,
    Grid,
    Stack,
} from '@mui/material';
import {
    Add as AddIcon,
    Delete as DeleteIcon,
    ArrowBack as ArrowBackIcon,
    Upload as UploadIcon,
    Download as DownloadIcon,
} from '@mui/icons-material';
import { router } from '@inertiajs/react';
import axios from 'axios';

const SubscriberList = ({ listId }) => {
    const [list, setList] = useState(null);
    const [subscribers, setSubscribers] = useState([]);
    const [openDialog, setOpenDialog] = useState(false);
    const [importDialog, setImportDialog] = useState(false);
    const [importFile, setImportFile] = useState(null);
    const [formData, setFormData] = useState({
        email: '',
        firstName: '',
        lastName: '',
    });

    useEffect(() => {
        fetchList();
    }, [listId]);

    const fetchList = async () => {
        try {
            const response = await axios.get(`/api/lists/${listId}`);
            setList(response.data);
            setSubscribers(response.data.subscribers);
        } catch (error) {
            console.error('Error fetching list:', error);
        }
    };

    const handleOpenDialog = () => {
        setFormData({
            email: '',
            firstName: '',
            lastName: '',
        });
        setOpenDialog(true);
    };

    const handleCloseDialog = () => {
        setOpenDialog(false);
        setFormData({
            email: '',
            firstName: '',
            lastName: '',
        });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        try {
            await axios.post(`/api/lists/${listId}/subscribers`, {
                subscribers: [formData],
            });
            handleCloseDialog();
            fetchList();
        } catch (error) {
            console.error('Error adding subscriber:', error);
        }
    };

    const handleDelete = async (email) => {
        if (window.confirm('Are you sure you want to remove this subscriber?')) {
            try {
                await axios.delete(`/api/lists/${listId}/subscribers`, {
                    data: { email },
                });
                fetchList();
            } catch (error) {
                console.error('Error removing subscriber:', error);
            }
        }
    };

    const handleStatusChange = async (email, newStatus) => {
        try {
            await axios.put(`/api/lists/${listId}/subscribers/status`, {
                email,
                status: newStatus,
            });
            fetchList();
        } catch (error) {
            console.error('Error updating subscriber status:', error);
        }
    };

    const handleImport = async (e) => {
        e.preventDefault();
        if (!importFile) return;

        const formData = new FormData();
        formData.append('file', importFile);

        try {
            await axios.post(`/api/lists/${listId}/import`, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
            });
            setImportDialog(false);
            setImportFile(null);
            fetchList();
        } catch (error) {
            console.error('Error importing subscribers:', error);
        }
    };

    const handleExport = async () => {
        try {
            const response = await axios.get(`/api/lists/${listId}/export`, {
                responseType: 'blob',
            });
            
            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', `${list.name}-subscribers.csv`);
            document.body.appendChild(link);
            link.click();
            link.remove();
        } catch (error) {
            console.error('Error exporting subscribers:', error);
        }
    };

    if (!list) {
        return <Typography>Loading...</Typography>;
    }

    return (
        <Box sx={{ p: 3 }}>
            <Box sx={{ display: 'flex', alignItems: 'center', mb: 3 }}>
                <IconButton onClick={() => router.visit('/lists')} sx={{ mr: 2 }}>
                    <ArrowBackIcon />
                </IconButton>
                <Typography variant="h4">{list.name}</Typography>
            </Box>

            <Card sx={{ mb: 3 }}>
                <CardContent>
                    <Typography variant="h6" gutterBottom>
                        List Details
                    </Typography>
                    <Typography color="textSecondary" gutterBottom>
                        {list.description}
                    </Typography>
                    <Stack direction="row" spacing={2} sx={{ mt: 2 }}>
                        <Button
                            variant="contained"
                            startIcon={<AddIcon />}
                            onClick={handleOpenDialog}
                        >
                            Add Subscriber
                        </Button>
                        <Button
                            variant="outlined"
                            startIcon={<UploadIcon />}
                            onClick={() => setImportDialog(true)}
                        >
                            Import Subscribers
                        </Button>
                        <Button
                            variant="outlined"
                            startIcon={<DownloadIcon />}
                            onClick={handleExport}
                        >
                            Export Subscribers
                        </Button>
                    </Stack>
                </CardContent>
            </Card>

            <TableContainer component={Paper}>
                <Table>
                    <TableHead>
                        <TableRow>
                            <TableCell>Email</TableCell>
                            <TableCell>Name</TableCell>
                            <TableCell>Status</TableCell>
                            <TableCell>Subscribed</TableCell>
                            <TableCell>Actions</TableCell>
                        </TableRow>
                    </TableHead>
                    <TableBody>
                        {subscribers.map((subscriber) => (
                            <TableRow key={subscriber.email}>
                                <TableCell>{subscriber.email}</TableCell>
                                <TableCell>
                                    {subscriber.first_name} {subscriber.last_name}
                                </TableCell>
                                <TableCell>
                                    <Chip
                                        label={subscriber.status}
                                        color={
                                            subscriber.status === 'active'
                                                ? 'success'
                                                : subscriber.status === 'unsubscribed'
                                                ? 'error'
                                                : 'warning'
                                        }
                                        onClick={() =>
                                            handleStatusChange(
                                                subscriber.email,
                                                subscriber.status === 'active'
                                                    ? 'unsubscribed'
                                                    : 'active'
                                            )
                                        }
                                    />
                                </TableCell>
                                <TableCell>
                                    {new Date(subscriber.subscribed_at).toLocaleDateString()}
                                </TableCell>
                                <TableCell>
                                    <IconButton
                                        size="small"
                                        onClick={() => handleDelete(subscriber.email)}
                                    >
                                        <DeleteIcon />
                                    </IconButton>
                                </TableCell>
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>
            </TableContainer>

            <Dialog open={openDialog} onClose={handleCloseDialog} maxWidth="sm" fullWidth>
                <DialogTitle>Add Subscriber</DialogTitle>
                <form onSubmit={handleSubmit}>
                    <DialogContent>
                        <TextField
                            autoFocus
                            margin="dense"
                            label="Email"
                            type="email"
                            fullWidth
                            required
                            value={formData.email}
                            onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                        />
                        <TextField
                            margin="dense"
                            label="First Name"
                            fullWidth
                            value={formData.firstName}
                            onChange={(e) => setFormData({ ...formData, firstName: e.target.value })}
                        />
                        <TextField
                            margin="dense"
                            label="Last Name"
                            fullWidth
                            value={formData.lastName}
                            onChange={(e) => setFormData({ ...formData, lastName: e.target.value })}
                        />
                    </DialogContent>
                    <DialogActions>
                        <Button onClick={handleCloseDialog}>Cancel</Button>
                        <Button type="submit" variant="contained">
                            Add
                        </Button>
                    </DialogActions>
                </form>
            </Dialog>

            <Dialog open={importDialog} onClose={() => setImportDialog(false)} maxWidth="sm" fullWidth>
                <DialogTitle>Import Subscribers</DialogTitle>
                <form onSubmit={handleImport}>
                    <DialogContent>
                        <Typography variant="body2" color="textSecondary" gutterBottom>
                            Upload a CSV file with the following columns: email, first_name, last_name
                        </Typography>
                        <Button
                            variant="outlined"
                            component="label"
                            fullWidth
                            sx={{ mt: 2 }}
                        >
                            Choose File
                            <input
                                type="file"
                                hidden
                                accept=".csv"
                                onChange={(e) => setImportFile(e.target.files[0])}
                            />
                        </Button>
                        {importFile && (
                            <Typography variant="body2" sx={{ mt: 1 }}>
                                Selected file: {importFile.name}
                            </Typography>
                        )}
                    </DialogContent>
                    <DialogActions>
                        <Button onClick={() => setImportDialog(false)}>Cancel</Button>
                        <Button
                            type="submit"
                            variant="contained"
                            disabled={!importFile}
                        >
                            Import
                        </Button>
                    </DialogActions>
                </form>
            </Dialog>
        </Box>
    );
};

export default SubscriberList; 