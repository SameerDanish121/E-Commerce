// src/socket/eventHandler.js
import { useToast } from 'vue-toast-notification';
import { useAdminProductStore } from '../js/stores/adminProductStore';
import { useOrderProcessingStore } from '../js/stores/OrderProcessingStore';
import { useAllAdminStore } from '../js/stores/AllAdminStore';
/**
 * Handles all socket.io events generically.
 * @param {Socket} socket - The Socket.IO instance.
 */
export function setupSocketEventListeners(socket) {
    socket.onAny((eventName, data) => {
        const toast = useToast();
        const adminProductStore = useAdminProductStore();
        const orderStore = useOrderProcessingStore();
        const adminStore = useAllAdminStore();
        console.log(`📡 Event Received: ${eventName}`, data);
        toast.success(
            `📢 [${eventName}] ${data?.message || "New event received!"}`,
            {
                position: 'top-right',
                duration: 10000,
                dismissible: true,
            }
        );
        switch (eventName) {
            case "OrderPlaced":
                adminProductStore.fetchProducts();
                orderStore.fetchAllOrders();
                break;
            case "permission-events":
                  adminStore.fetchAllAdmins();
                  adminStore.fetchAllPermissions();
                break;
            case "admin-events":
                   adminStore.fetchAllAdmins();
                break;
            case "product-events":
                orderStore.fetchAllOrders();
                adminProductStore.fetchProducts();
                break;
            default:
                console.log(`No handler for event: ${eventName}`);
        }
    });
}
