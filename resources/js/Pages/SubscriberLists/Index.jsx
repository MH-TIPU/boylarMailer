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
    Grid,
    Chip,
} from '@mui/material';
import {
    Add as AddIcon,
    Edit as EditIcon,
    Delete as DeleteIcon,
    People as PeopleIcon,
} from '@mui/icons-material';
import { router } from '@inertiajs/react';
import axios from 'axios';

const SubscriberLists = () => {
    const [lists, setLists] = useState([]);
    const [openDialog, setOpenDialog] = useState(false);
    const [editingList, setEditingList] = useState(null);
    const [formData, setFormData] = useState({
        name: '',
        description: '',
    });

    useEffect(() => {
        fetchLists();
    }, []);

    const fetchLists = async () => {
        try {
            const response = await axios.get('/api/lists');
            setLists(response.data);
        } catch (error) {
            console.error('Error fetching lists:', error);
        }
    };

    const handleOpenDialog = (list = null) => {
        if (list) {
            setEditingList(list);
            setFormData({
                name: list.name,
                description: list.description || '',
            });
        } else {
            setEditingList(null);
            setFormData({
                name: '',
                description: '',
            });
        }
        setOpenDialog(true);
    };

    const handleCloseDialog = () => {
        setOpenDialog(false);
        setEditingList(null);
        setFormData({
            name: '',
            description: '',
        });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        try {
            if (editingList) {
                await axios.put(`/api/lists/${editingList.id}`, formData);
            } else {
                await axios.post('/api/lists', formData);
            }
            handleCloseDialog();
            fetchLists();
        } catch (error) {
            console.error('Error saving list:', error);
        }
    };

    const handleDelete = async (id) => {
        if (window.confirm('Are you sure you want to delete this list?')) {
            try {
                await axios.delete(`/api/lists/${id}`);
                fetchLists();
            } catch (error) {
                console.error('Error deleting list:', error);
            }
        }
    };

    return (
        <Box sx={{ p: 3 }}>
            <Box sx={{ display: 'flex', justifyContent: 'space-between', mb: 3 }}>
                <Typography variant="h4">Subscriber Lists</Typography>
                <Button
                    variant="contained"
                    startIcon={<AddIcon />}
                    onClick={() => handleOpenDialog()}
                >
                    Create List
                </Button>
            </Box>

            <Grid container spacing={3}>
                {lists.map((list) => (
                    <Grid item xs={12} md={6} lg={4} key={list.id}>
                        <Card>
                            <CardContent>
                                <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start' }}>
                                    <Typography variant="h6" gutterBottom>
                                        {list.name}
                                    </Typography>
                                    <Box>
                                        <IconButton
                                            size="small"
                                            onClick={() => handleOpenDialog(list)}
                                        >
                                            <EditIcon />
                                        </IconButton>
                                        <IconButton
                                            size="small"
                                            onClick={() => handleDelete(list.id)}
                                        >
                                            <DeleteIcon />
                                        </IconButton>
                                    </Box>
                                </Box>
                                <Typography color="textSecondary" gutterBottom>
                                    {list.description}
                                </Typography>
                                <Box sx={{ mt: 2, display: 'flex', alignItems: 'center', gap: 1 }}>
                                    <PeopleIcon fontSize="small" />
                                    <Typography variant="body2">
                                        {list.subscribers_count} subscribers
                                    </Typography>
                                </Box>
                                <Box sx={{ mt: 2 }}>
                                    <Button
                                        variant="outlined"
                                        size="small"
                                        onClick={() => router.visit(`/lists/${list.id}`)}
                                    >
                                        Manage Subscribers
                                    </Button>
                                </Box>
                            </CardContent>
                        </Card>
                    </Grid>
                ))}
            </Grid>

            <Dialog open={openDialog} onClose={handleCloseDialog} maxWidth="sm" fullWidth>
                <DialogTitle>
                    {editingList ? 'Edit List' : 'Create New List'}
                </DialogTitle>
                <form onSubmit={handleSubmit}>
                    <DialogContent>
                        <TextField
                            autoFocus
                            margin="dense"
                            label="List Name"
                            fullWidth
                            required
                            value={formData.name}
                            onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                        />
                        <TextField
                            margin="dense"
                            label="Description"
                            fullWidth
                            multiline
                            rows={3}
                            value={formData.description}
                            onChange={(e) => setFormData({ ...formData, description: e.target.value })}
                        />
                    </DialogContent>
                    <DialogActions>
                        <Button onClick={handleCloseDialog}>Cancel</Button>
                        <Button type="submit" variant="contained">
                            {editingList ? 'Update' : 'Create'}
                        </Button>
                    </DialogActions>
                </form>
            </Dialog>
        </Box>
    );
};

export default SubscriberLists; 