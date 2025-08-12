import { defineStore } from 'pinia';
import { io } from "socket.io-client";
import { useToast } from 'vue-toast-notification';
import axios from 'axios';

export const useAdminChatBotStore = defineStore('adminChatBot', {
  persist: true,
  state: () => ({
    groupedMessages: [],
    socket: null,
    isConnected: false,
    toast: useToast(),
    selectedCustomer: null,
    unreadCounts: {},
    searchQuery: ''
  }),
  actions: {
    initializeSocket() {
      if (this.socket) return;
      
      this.socket = io("http://192.168.18.33:3001", {
        auth: {
          privateKey: "abc"
        }
      });

      this.setupSocketEventListeners();
    },
    setupSocketEventListeners() {
      this.socket.on("connect", () => {
        this.isConnected = true;
        console.log("Socket connected");
      });

      this.socket.on("disconnect", () => {
        this.isConnected = false;
        console.log("Socket disconnected");
      });

      this.socket.onAny((eventName, data) => {
        console.log(`📡 Event Received: ${eventName}`, data);
        this.socket.on("ChatMessageSent", (data) => this.handleSocketEvent('ChatMessageSent', data));
        this.socket.on("AdminMessageSent", (data) => this.handleSocketEvent('AdminMessageSent', data));
        this.socket.on("ChatMessageUpdated", (data) => this.handleSocketEvent('ChatMessageUpdated', data));
        this.socket.on("ChatMessageDeleted", (data) => this.handleSocketEvent('ChatMessageDeleted', data));

        this.socket.on("AdminMessageUpdated", (data) => this.handleSocketEvent('ChatMessageUpdated', data));
        this.socket.on("AdminMessageDeleted", (data) => this.handleSocketEvent('ChatMessageDeleted', data));
      });
    },
    
    async handleSocketEvent(event, data) {
      const customer = this.getCustomerById(data.customer_id);
      const customerName = customer?.name || 'a customer';
      
      switch (event) {
        case 'ChatMessageSent':
          if (data.sender_type === 'customer') {
            this.incrementUnreadCount(data.customer_id);
            
            if (this.selectedCustomer?.id !== data.customer_id) {
              // this.showNotification(
              //   'New message',
              //   `${customerName} sent a new message`,
              //   'info'
              // );
            }
          }
          await this.fetchGroupedMessages();
          break;
          
        case 'AdminMessageSent':
          await this.fetchGroupedMessages();
          break;
          
        case 'ChatMessageUpdated':
          this.updateMessageInStore(data);
          break;
          
        case 'ChatMessageDeleted':
          this.deleteMessageInStore(data.message_id);
          break;
      }
    },
    
    incrementUnreadCount(customerId) {
      if (!this.unreadCounts[customerId]) {
        this.unreadCounts[customerId] = 0;
      }
      this.unreadCounts[customerId]++;
    },
    
    resetUnreadCount(customerId) {
      this.unreadCounts[customerId] = 0;
    },
    
    // showNotification(title, message, type = 'info') {
    //   this.toast[type](`📢 ${title}: ${message}`, {
    //     position: 'top-right',
    //     duration: 10000,
    //     dismissible: true,
    //   });
    // },
    
    updateMessageInStore(updatedMessage) {
      this.groupedMessages = this.groupedMessages.map(group => {
        if (group.customer.id === updatedMessage.customer_id) {
          return {
            ...group,
            messages: group.messages.map(msg => {
              if (msg.id === updatedMessage.message_id) {
                return {
                  ...msg,
                  message: updatedMessage.message,
                  media_type: updatedMessage.media_type,
                  media_url: updatedMessage.media_url,
                  reply_to: updatedMessage.reply_to,
                  updated_at: updatedMessage.timestamp
                };
              }
              return msg;
            })
          };
        }
        return group;
      });
    },
    
    deleteMessageInStore(messageId) {
      this.groupedMessages = this.groupedMessages.map(group => ({
        ...group,
        messages: group.messages.map(msg => {
          if (msg.id === messageId) {
            return {
              ...msg,
              message: null,
              is_deleted: true
            };
          }
          return msg;
        })
      }));
    },
    
    async fetchGroupedMessages() {
      try {
        const response = await axios.get('/api/chat_admin/grouped');
        this.groupedMessages = response.data;
        this.sortCustomersByLastMessage();
      } catch (error) {
        // console.error("Error fetching grouped messages:", error);
        // this.showNotification(
        //   'Error',
        //   'Failed to load messages',
        //   'error'
        // );
      }
    },
    
    sortCustomersByLastMessage() {
      this.groupedMessages.sort((a, b) => {
        const aLastMsg = a.messages[a.messages.length - 1]?.created_at || 0;
        const bLastMsg = b.messages[b.messages.length - 1]?.created_at || 0;
        return new Date(bLastMsg) - new Date(aLastMsg);
      });
    },
    
    async selectCustomer(customer) {
      this.selectedCustomer = customer;
      this.resetUnreadCount(customer.id);
      await this.markMessagesAsRead(customer.id);
    },
    
    async markMessagesAsRead(customerId) {
      try {
        await axios.post(`/api/chat_admin/mark-read/${customerId}`);
        await this.fetchGroupedMessages();
      } catch (error) {
        console.error("Error marking messages as read:", error);
      }
    },
    
    async sendMessage(messageData) {
      try {
        const formData = new FormData();
        formData.append('customer_id', messageData.customer_id);
        if (messageData.message) formData.append('message', messageData.message);
        if (messageData.media) formData.append('media', messageData.media);
        if (messageData.media_type) formData.append('media_type', messageData.media_type);
        if (messageData.reply_to) formData.append('reply_to', messageData.reply_to);

        const response = await axios.post('/api/chat_admin/send', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        });

        return response.data;
      } catch (error) {
        console.error("Error sending message:", error);
        // this.showNotification(
        //   'Error',
        //   'Failed to send message',
        //   'error'
        // );
        throw error;
      }
    },
    
    async updateMessage(messageId, updateData) {
      try {
        const formData = new FormData();
        if (updateData.message) formData.append('message', updateData.message);
        if (updateData.media) formData.append('media', updateData.media);
        if (updateData.media_type) formData.append('media_type', updateData.media_type);
        if (updateData.reply_to) formData.append('reply_to', updateData.reply_to);

        const response = await axios.post(`/api/chat_admin/update/${messageId}`, formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        });

        return response.data;
      } catch (error) {
        console.error("Error updating message:", error);
        // this.showNotification(
        //   'Error',
        //   'Failed to update message',
        //   'error'
        // );
        throw error;
      }
    },
    
    async deleteMessage(messageId) {
      try {
        const response = await axios.delete(`/api/chat_admin/delete/${messageId}`);
        return response.data;
      } catch (error) {
        console.error("Error deleting message:", error);
        // this.showNotification(
        //   'Error',
        //   'Failed to delete message',
        //   'error'
        // );
        throw error;
      }
    },
    
    disconnectSocket() {
      if (this.socket) {
        this.socket.disconnect();
        this.socket = null;
        this.isConnected = false;
      }
    },
    
    getMessagesForCustomer(customerId) {
      const group = this.groupedMessages.find(g => g.customer.id === customerId);
      return group ? group.messages : [];
    },
    
    getCustomerById(customerId) {
      const group = this.groupedMessages.find(g => g.customer.id === customerId);
      return group ? group.customer : null;
    },
    
    getFilteredCustomers() {
      if (!this.searchQuery) {
        return this.groupedMessages;
      }
      
      const query = this.searchQuery.toLowerCase();
      return this.groupedMessages.filter(group => 
        group.customer.name.toLowerCase().includes(query) || 
        group.customer.email?.toLowerCase().includes(query)
      );
    }
  }
});