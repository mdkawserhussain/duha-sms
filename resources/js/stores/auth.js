import { defineStore } from 'pinia';
import api from '../services/api';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        token: localStorage.getItem('auth_token') || null,
        loading: false,
        error: null,
    }),

    getters: {
        isAuthenticated: (state) => !!state.token,
        userRole: (state) => state.user?.role || null,
        isAdmin: (state) => state.user?.role === 'admin',
        isTeacher: (state) => state.user?.role === 'teacher',
        isGuardian: (state) => state.user?.role === 'guardian',
    },

    actions: {
        async login(credentials) {
            this.loading = true;
            this.error = null;
            try {
                const response = await api.post('/auth/login', credentials);
                this.token = response.data.token;
                this.user = response.data.user;
                localStorage.setItem('auth_token', this.token);
                localStorage.setItem('user_role', this.user.role);
                return true;
            } catch (error) {
                this.error = error.response?.data?.message || 'Login failed';
                return false;
            } finally {
                this.loading = false;
            }
        },

        async register(userData) {
            this.loading = true;
            this.error = null;
            try {
                const response = await api.post('/auth/register', userData);
                this.token = response.data.token;
                this.user = response.data.user;
                localStorage.setItem('auth_token', this.token);
                localStorage.setItem('user_role', this.user.role);
                return true;
            } catch (error) {
                this.error = error.response?.data?.message || 'Registration failed';
                return false;
            } finally {
                this.loading = false;
            }
        },

        async logout() {
            try {
                await api.post('/auth/logout');
            } catch (error) {
                console.error('Logout error:', error);
            } finally {
                this.token = null;
                this.user = null;
                localStorage.removeItem('auth_token');
                localStorage.removeItem('user_role');
            }
        },

        async fetchUser() {
            if (!this.token) return;
            this.loading = true;
            try {
                const response = await api.get('/auth/user');
                this.user = response.data;
                localStorage.setItem('user_role', this.user.role);
            } catch (error) {
                this.token = null;
                this.user = null;
                localStorage.removeItem('auth_token');
                localStorage.removeItem('user_role');
            } finally {
                this.loading = false;
            }
        },

        async changePassword(passwords) {
            this.loading = true;
            this.error = null;
            try {
                await api.put('/auth/password', passwords);
                return true;
            } catch (error) {
                this.error = error.response?.data?.message || 'Password change failed';
                return false;
            } finally {
                this.loading = false;
            }
        },
    },
});
