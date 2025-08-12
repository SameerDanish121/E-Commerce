import { defineStore } from 'pinia';
import { io } from "socket.io-client";
export const useCustomerChatBotStore = defineStore('customerChatBot', {
  persist: true,
  state: () => ({
    messages: [],
    socket: null,
    isConnected: false,
    currentCustomerId: null,
    messageGroups: {}
  }),
  actions: {
    initializeSocket() {
      if (this.socket) return;
      this.socket = io("http://192.168.18.33:3001", {
        auth: {
          privateKey: "abc"
        },
      });
      this.setupSocketEventListeners(this.socket);
    },
    setupSocketEventListeners(socket) {
      socket.onAny((eventName, data) => {
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
      console.log('start');
      if (data.customer_id != this.currentCustomerId) {
        return;
      }
      switch (event) {
        case 'AdminMessageSent':
          this.addMessageToStore(data);
          break;
        case 'ChatMessageUpdated':
          this.updateMessageInStore(data);
          break;
        case 'ChatMessageDeleted':
          this.deleteMessageInStore(data.message_id);
          break;
        case 'ChatMessageSent':
          await this.fetchMessages(this.currentCustomerId);
          break;
        default:
          console.log('nassss');
      };
    },
    addMessageToStore(message) {
      const exists = this.messages.some(m => m.id === message.message_id);
      if (!exists) {
        this.messages.push({
          id: message.message_id,
          message: message.message,
          media_type: message.media_type,
          media_url: message.media_url,
          reply_to: message.reply_to,
          is_opened: message.is_opened,
          is_deleted: message.is_deleted,
          sender_type: message.sender_type,
          sender_id: message.sender_id,
          customer_id: message.customer_id,
          created_at: message.timestamp
        });
        this.groupMessagesByDate();
      }
    },

    updateMessageInStore(updatedMessage) {
      const index = this.messages.findIndex(m => m.id === updatedMessage.message_id);
      if (index !== -1) {
        this.messages[index] = {
          ...this.messages[index],
          message: updatedMessage.message,
          media_type: updatedMessage.media_type,
          media_url: updatedMessage.media_url,
          reply_to: updatedMessage.reply_to,
          updated_at: updatedMessage.timestamp
        };
        this.groupMessagesByDate();
      }
    },
    async updateMessage(messageId, updateData) {
      try {
        const formData = new FormData();
        if (updateData.message) formData.append('message', updateData.message);
        if (updateData.media) formData.append('media', updateData.media);
        if (updateData.media_type) formData.append('media_type', updateData.media_type);
        if (updateData.reply_to) formData.append('reply_to', updateData.reply_to);

        const response = await axios.post(`/api/chat_customer/update/${messageId}`, formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        });

        this.updateMessageInStore(response.data.data);

        return response.data;
      } catch (error) {
        console.error("Error updating message:", error);

        throw error;
      }
    },

    async deleteMessage(messageId) {
      try {
        const response = await axios.delete(`/api/chat_customer/delete/${messageId}`);

        this.deleteMessageInStore(messageId);
        return response.data;
      } catch (error) {
        console.error("Error deleting message:", error);
        this.showNotification(
          'Error',
          'Failed to delete message',
          'error'
        );
        throw error;
      }
    },
    deleteMessageInStore(messageId) {
      const index = this.messages.findIndex(m => m.id === messageId);
      if (index !== -1) {
        this.messages[index] = {
          ...this.messages[index],
          message: null,
          is_deleted: true,
          updated_at: new Date().toISOString()
        };
        this.groupMessagesByDate();
      }
    },
    async fetchMessages(customerId) {
      if (!customerId) {

        return;
      }

      try {
        this.currentCustomerId = customerId;
        const response = await axios.get(`/api/chat_customer/${customerId}`);
        this.messages = response.data;
        this.groupMessagesByDate();
      } catch (error) {
        console.error("Error fetching messages:", error);
        this.showNotification('Error', 'Failed to load messages', 'error');
      }
    },

    async sendMessage(messageData) {
      try {
        const formData = new FormData();
        formData.append('customer_id', this.currentCustomerId);
        formData.append('sender_type', 'customer');
        formData.append('sender_id', messageData.sender_id);
        if (messageData.message) formData.append('message', messageData.message);
        if (messageData.media) formData.append('media', messageData.media);
        if (messageData.media_type) formData.append('media_type', messageData.media_type);
        if (messageData.reply_to) formData.append('reply_to', messageData.reply_to);

        const response = await axios.post('/api/chat_customer/send', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        });

        return response.data;
      } catch (error) {
        console.error("Error sending message:", error);
        let errorMsg = 'Failed to send message';
        if (error.response?.data?.message) {
          errorMsg = error.response.data.message;
        }
        throw error;
      }
    },

    groupMessagesByDate() {
      const groups = {};
      this.messages.forEach(message => {
        const date = new Date(message.created_at);
        const dateKey = date.toLocaleDateString();

        if (!groups[dateKey]) {
          groups[dateKey] = [];
        }
        groups[dateKey].push(message);
      });
      this.messageGroups = groups;
    },

    disconnectSocket() {
      if (this.socket) {
        this.socket.disconnect();
        this.socket = null;
        this.isConnected = false;
      }
    }
  }
});