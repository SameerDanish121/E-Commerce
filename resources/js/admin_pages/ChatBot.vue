
<template>
  <div class="admin-chat-container" :class="{ 'dark-theme': darkMode, 'mobile-view': isMobile }">
    <div class="mobile-header" v-if="isMobile && selectedCustomer">
      <button class="back-button" @click="closeChat">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
          <path fill="currentColor" d="M20 11H7.83l5.59-5.59L12 4l-8 8l8 8l1.41-1.41L7.83 13H20z"/>
        </svg>
      </button>
      <div class="profile-info">
        <div class="customer-avatar">
          <img :src="selectedCustomer.profile_pic || 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png'" 
               :alt="selectedCustomer.name">
        </div>
        <h3>{{ selectedCustomer.name }}</h3>
      </div>
      <button class="more-options" @click="toggleCustomerList">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
          <path fill="currentColor" d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/>
        </svg>
      </button>
    </div>
    <div class="customer-list-panel" :class="{ 'mobile-drawer': isMobile, 'drawer-open': showCustomerList }">
      <div class="customer-list-header">
        <div class="search-container">
          <input v-model="searchQuery" type="text" placeholder="Search customers..." class="search-input">
          <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
            <path fill="currentColor" d="M15.5 14h-.79l-.28-.27a6.5 6.5 0 0 0 1.48-5.34c-.47-2.78-2.79-5-5.59-5.34a6.505 6.505 0 0 0-7.27 7.27c.34 2.8 2.56 5.12 5.34 5.59a6.5 6.5 0 0 0 5.34-1.48l.27.28v.79l4.25 4.25c.41.41 1.08.41 1.49 0c.41-.41.41-1.08 0-1.49L15.5 14zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5S14 7.01 14 9.5S11.99 14 9.5 14z"/>
          </svg>
        </div>
        <button @click="toggleDarkMode" class="theme-toggle">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
            <path fill="currentColor" d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1-8.313-12.454z"/>
          </svg>
        </button>
      </div>
      
      <div class="customer-list">
        <div v-for="group in filteredCustomers" :key="group.customer.id" @click="selectCustomer(group.customer)"
          :class="['customer-item', { 'active': selectedCustomer?.id === group.customer.id }]">
          <div class="customer-avatar">
            <img :src="group.customer.profile_pic || 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png'" 
                 :alt="group.customer.name">
            <span v-if="unreadCounts[group.customer.id]" class="unread-badge">
              {{ unreadCounts[group.customer.id] }}
            </span>
          </div>
          <div class="customer-info">
            <h4>{{ group.customer.name }}</h4>
            <p class="last-message">
              {{ getLastMessagePreview(group.messages) }}
            </p>
          </div>
          <div class="message-time">
            {{ formatLastMessageTime(group.messages) }}
          </div>
        </div>
      </div>
    </div>
  
    <div class="chat-area-panel" v-if="selectedCustomer">
      <div class="chat-header" v-if="!isMobile">
        <div class="profile-info">
          <div class="customer-avatar">
            <img :src="selectedCustomer.profile_pic || 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png'" 
                 :alt="selectedCustomer.name">
          </div>
          <div>
            <h3>{{ selectedCustomer.name }}</h3>
            <p class="connection-status" :class="{ 'online': isConnected }">
              {{ isConnected ? 'Online' : 'Offline' }}
            </p>
          </div>
        </div>
        <div class="header-actions">
          <button class="more-options" @click="closeChat">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
              <path fill="currentColor" d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12z"/>
            </svg>
          </button>
        </div>
      </div>
      
      <div class="chat-messages" ref="messagesContainer">
        <template v-for="(messages, date) in messageGroups" :key="date">
          <div class="date-divider">
            {{ formatDateHeader(date) }}
          </div>
          <div v-for="message in messages" :key="message.id"
            :class="['message', message.sender_type === 'admin' ? 'sent' : 'received']">
            <div class="message-content">
              <div v-if="message.sender_type !== 'admin'" class="message-avatar">
                <img :src="selectedCustomer.profile_pic || 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png'" 
                     :alt="selectedCustomer.name">
              </div>

              <div class="message-bubble">
                <div v-if="message.reply_to" class="reply-preview">
                  <p>{{ getRepliedMessage(message.reply_to) }}</p>
                </div>

                <p v-if="message.message && !message.is_deleted">{{ message.message }}</p>
                <p v-else-if="message.is_deleted" class="deleted-message">This message has been deleted</p>

                <div v-if="message.media_url && !message.is_deleted" class="media-container">
                  <img v-if="message.media_type === 'image'" :src="message.media_url" alt="Image"
                    @click="openMediaViewer(message.media_url, 'image')">
                  <video v-else-if="message.media_type === 'video'" controls>
                    <source :src="message.media_url" type="video/mp4">
                  </video>
                  <a v-else-if="message.media_type === 'file'" :href="message.media_url" target="_blank"
                    class="file-attachment">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                      <path fill="currentColor" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                      <path fill="currentColor" d="M14 2v6h6m-4 5H8m8 4H8m2-8H8"/>
                    </svg>
                    Download File
                  </a>
                </div>

                <div class="message-meta">
                  <span class="time">{{ formatTime(message.created_at) }}</span>
                  <span v-if="message.sender_type === 'admin'" class="status">
                    <span v-if="message.is_opened" class="double-tick">
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                        <path fill="#4fc3f7" d="M18 7l-8 8l-4-4l-1.5 1.5L10 17l9.5-9.5z"/>
                        <path fill="#4fc3f7" d="M11.5 15.5L17 10l-1.5-1.5l-4 4l-2-2l-1.5 1.5z"/>
                      </svg>
                    </span>
                    <span v-else class="single-tick">
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M18 7l-8 8l-4-4l-1.5 1.5L10 17l9.5-9.5z"/>
                      </svg>
                    </span>
                  </span>
                </div>
              </div>

              <div v-if="message.sender_type === 'admin'" class="message-actions">
                <div class="dropdown">
                  <button class="dropdown-toggle">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                      <path fill="currentColor" d="M12 16a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2m0-6a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2m0-6a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2z"/>
                    </svg>
                  </button>
                  <div class="dropdown-menu">
                    <button @click="editMessage(message)" v-if="!message.is_deleted">Edit</button>
                    <button @click="deleteMessage(message.id)">Delete</button>
                    <button @click="replyToMessage(message)" v-if="!message.is_deleted">Reply</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </template>
      </div>
    
      <div class="message-input">
        <button class="attachment-btn" @click="toggleAttachmentMenu">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
            <path fill="currentColor" d="M18 15v3H6v-3H4v3c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2v-3zm-1-4l-1.41-1.41L13 12.17V4h-2v8.17L8.41 9.59L7 11l5 5z"/>
          </svg>
        </button>

        <div class="attachment-menu" v-if="showAttachmentMenu">
          <button @click="triggerFileInput('image')">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
              <path fill="currentColor" d="M5 21q-.825 0-1.412-.587T3 19V5q0-.825.588-1.412T5 3h14q.825 0 1.413.588T21 5v14q0 .825-.587 1.413T19 21zm0-2h14V5H5v14zm1-2h12l-3.75-5l-3 4L9 13zm-1 2V5v14z"/>
            </svg>
            Photo
          </button>
          <button @click="triggerFileInput('video')">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
              <path fill="currentColor" d="M17 10.5V7c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1v-3.5l4 4v-11z"/>
            </svg>
            Video
          </button>
          <button @click="triggerFileInput('file')">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
              <path fill="currentColor" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
              <path fill="currentColor" d="M14 2v6h6m-4 5H8m8 4H8m2-8H8"/>
            </svg>
            Document
          </button>
        </div>

        <input type="file" ref="fileInput" style="display: none;" @change="handleFileUpload">

        <div class="input-wrapper">
          <input type="text" v-model="newMessage" @keyup.enter="sendMessage"
            :placeholder="replyingTo ? `Replying to: ${replyingTo.message.substring(0, 20)}...` : 'Type a message...'">

          <div v-if="replyingTo" class="reply-preview-input">
            <p>{{ replyingTo.message.substring(0, 30) }}{{ replyingTo.message.length > 30 ? '...' : '' }}</p>
            <button @click="cancelReply">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                <path fill="currentColor" d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12z"/>
              </svg>
            </button>
          </div>

          <div v-if="selectedFile" class="file-preview">
            <span>{{ selectedFile.name }}</span>
            <button @click="removeFile">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                <path fill="currentColor" d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12z"/>
              </svg>
            </button>
          </div>
        </div>

        <button class="send-btn" @click="sendMessage" :disabled="!newMessage && !selectedFile">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
            <path fill="currentColor" d="M2.01 21L23 12L2.01 3L2 10l15 2l-15 2z"/>
          </svg>
        </button>
      </div>
    </div>

    <div v-else class="empty-chat">
      <div class="empty-content">
        <img src="https://cdn-icons-png.flaticon.com/512/4712/4712035.png" alt="Chat">
        <h3>Select a customer to start chatting</h3>
        <p>Choose from the list on the left to view and reply to messages</p>
      </div>
    </div>
   
    <div v-if="showMediaViewer" class="media-viewer" @click="closeMediaViewer">
      <div class="media-content" @click.stop>
        <img v-if="viewingMediaType === 'image'" :src="viewingMediaUrl" alt="Enlarged media">
        <video v-else-if="viewingMediaType === 'video'" controls autoplay>
          <source :src="viewingMediaUrl" type="video/mp4">
        </video>
        <button class="close-viewer" @click="closeMediaViewer">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
            <path fill="currentColor" d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12z"/>
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { useAdminChatBotStore } from '../stores/Chat/AdminChatStore.js';
import { onMounted, ref, watch, nextTick, computed } from 'vue';

export default {
  setup() {
    const chatStore = useAdminChatBotStore();

    const messagesContainer = ref(null);
    const newMessage = ref('');
    const replyingTo = ref(null);
    const editingMessage = ref(null);
    const showAttachmentMenu = ref(false);
    const fileInput = ref(null);
    const selectedFile = ref(null);
    const fileType = ref('');
    const darkMode = ref(false);
    const showMediaViewer = ref(false);
    const viewingMediaUrl = ref('');
    const viewingMediaType = ref('');
    const searchQuery = ref('');
    const showCustomerList = ref(false);
    const isMobile = ref(window.innerWidth <= 768);

    const handleResize = () => {
      isMobile.value = window.innerWidth <= 768;
    };

    onMounted(async () => {
      await chatStore.fetchGroupedMessages();
      chatStore.initializeSocket();
      scrollToBottom();
      window.addEventListener('resize', handleResize);
    });

    watch(() => chatStore.selectedCustomer, (newVal) => {
      if (newVal) {
        if (isMobile.value) {
          showCustomerList.value = false;
        }
        nextTick(() => {
          scrollToBottom();
        });
      }
    });

    watch(() => chatStore.groupedMessages, () => {
      nextTick(() => {
        scrollToBottom();
      });
    }, { deep: true });

    const filteredCustomers = computed(() => chatStore.getFilteredCustomers());
    
    const messageGroups = computed(() => {
      if (!chatStore.selectedCustomer) return {};
      
      const messages = chatStore.getMessagesForCustomer(chatStore.selectedCustomer.id);
      const groups = {};
      
      messages.forEach(message => {
        const date = new Date(message.created_at);
        const dateKey = date.toLocaleDateString();

        if (!groups[dateKey]) {
          groups[dateKey] = [];
        }
        groups[dateKey].push(message);
      });
      
      return groups;
    });

    const formatTime = (dateString) => {
      const date = new Date(dateString);
      return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true });
    };

    const formatDateHeader = (dateString) => {
      const date = new Date(dateString);
      const today = new Date();
      const yesterday = new Date(today);
      yesterday.setDate(yesterday.getDate() - 1);

      if (date.toDateString() === today.toDateString()) {
        return 'Today';
      } else if (date.toDateString() === yesterday.toDateString()) {
        return 'Yesterday';
      } else {
        return date.toLocaleDateString([], { day: 'numeric', month: 'long', year: 'numeric' });
      }
    };

    const scrollToBottom = () => {
      if (messagesContainer.value) {
        messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
      }
    };

    const sendMessage = async () => {
      if (!newMessage.value && !selectedFile.value) return;

      try {
        const messageData = {
          customer_id: chatStore.selectedCustomer.id,
          message: newMessage.value,
          reply_to: replyingTo.value?.id || null,
          media: selectedFile.value,
          media_type: fileType.value
        };
        
        if (editingMessage.value != null) {
          await chatStore.updateMessage(editingMessage.value.id, messageData);
        } else {
          await chatStore.sendMessage(messageData);
        }
        
        newMessage.value = '';
        selectedFile.value = null;
        replyingTo.value = null;
        editingMessage.value = null;
        showAttachmentMenu.value = false;
      } catch (error) {
        console.error("Error sending message:", error);
      }
    };

    const replyToMessage = (message) => {
      replyingTo.value = message;
    };

    const cancelReply = () => {
      replyingTo.value = null;
    };

    const editMessage = (message) => {
      editingMessage.value = message;
      newMessage.value = message.message;
      replyingTo.value = null;
    };

    const deleteMessage = async (messageId) => {
      if (confirm('Are you sure you want to delete this message?')) {
        await chatStore.deleteMessage(messageId);
      }
    };

    const toggleAttachmentMenu = () => {
      showAttachmentMenu.value = !showAttachmentMenu.value;
    };

    const triggerFileInput = (type) => {
      fileType.value = type;
      fileInput.value.click();
      showAttachmentMenu.value = false;
    };

    const handleFileUpload = (event) => {
      selectedFile.value = event.target.files[0];
      event.target.value = '';
    };

    const removeFile = () => {
      selectedFile.value = null;
    };

    const toggleDarkMode = () => {
      darkMode.value = !darkMode.value;
    };

    const openMediaViewer = (url, type) => {
      viewingMediaUrl.value = url;
      viewingMediaType.value = type;
      showMediaViewer.value = true;
    };

    const closeMediaViewer = () => {
      showMediaViewer.value = false;
      viewingMediaUrl.value = '';
      viewingMediaType.value = '';
    };

    const getRepliedMessage = (messageId) => {
      const messages = chatStore.getMessagesForCustomer(chatStore.selectedCustomer.id);
      const message = messages.find(m => m.id === messageId);
      return message ? (message.is_deleted ? 'Deleted message' : message.message) : 'Original message not found';
    };
    
    const selectCustomer = (customer) => {
      chatStore.selectCustomer(customer);
    };
    
    const getLastMessagePreview = (messages) => {
      if (messages.length === 0) return 'No messages yet';
      const lastMsg = messages[messages.length - 1];
      
      if (lastMsg.is_deleted) return 'Message deleted';
      if (lastMsg.media_type !== 'none') {
        return `📎 ${lastMsg.media_type.charAt(0).toUpperCase() + lastMsg.media_type.slice(1)}`;
      }
      return lastMsg.message.length > 30 
        ? lastMsg.message.substring(0, 30) + '...' 
        : lastMsg.message;
    };
    
    const formatLastMessageTime = (messages) => {
      if (messages.length === 0) return '';
      const lastMsg = messages[messages.length - 1];
      const date = new Date(lastMsg.created_at);
      return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false });
    };

    const toggleCustomerList = () => {
      showCustomerList.value = !showCustomerList.value;
    };

    const closeChat = () => {
      chatStore.selectedCustomer = null;
      if (isMobile.value) {
        showCustomerList.value = true;
      }
    };

    return {
      messagesContainer,
      newMessage,
      replyingTo,
      editingMessage,
      showAttachmentMenu,
      fileInput,
      selectedFile,
      darkMode,
      showMediaViewer,
      viewingMediaUrl,
      viewingMediaType,
      searchQuery,
      filteredCustomers,
      messageGroups,
      unreadCounts: computed(() => chatStore.unreadCounts),
      isConnected: computed(() => chatStore.isConnected),
      selectedCustomer: computed(() => chatStore.selectedCustomer),
      formatTime,
      formatDateHeader,
      sendMessage,
      replyToMessage,
      cancelReply,
      editMessage,
      deleteMessage,
      toggleAttachmentMenu,
      triggerFileInput,
      handleFileUpload,
      removeFile,
      toggleDarkMode,
      openMediaViewer,
      closeMediaViewer,
      getRepliedMessage,
      selectCustomer,
      getLastMessagePreview,
      formatLastMessageTime,
      isMobile,
      showCustomerList,
      toggleCustomerList,
      closeChat
    };
  }
};
</script>

<style scoped>
.admin-chat-container {
  display: flex;
  height: 100vh;
  width: 100%;
  background-color: #f0f2f5;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  transition: background-color 0.3s ease;
}

.admin-chat-container.mobile-view {
  flex-direction: column;
}

.dark-theme {
  background-color: #0a1014;
  color: #e1e1e1;
}

.dark-theme .customer-list-panel,
.dark-theme .chat-area-panel,
.dark-theme .empty-chat {
  background-color: #1f2c33;
  border-color: #2a3942;
}

.dark-theme .customer-item {
  border-bottom-color: #2a3942;
}

.dark-theme .customer-item:hover {
  background-color: #2a3942;
}

.dark-theme .customer-item.active {
  background-color: #005c4b;
}

.dark-theme .search-input {
  background-color: #2a3942;
  color: #e1e1e1;
}

.dark-theme .message-input input {
  background-color: #2a3942;
  color: #e1e1e1;
}

.dark-theme .message.received .message-bubble {
  background-color: #2a3942;
  color: #e1e1e1;
}

.dark-theme .message.sent .message-bubble {
  background-color: #005c4b;
  color: #e1e1e1;
}

.dark-theme .attachment-menu {
  background-color: #2a3942;
  border-color: #374248;
}

.dark-theme .attachment-menu button {
  color: #e1e1e1;
}

.dark-theme .dropdown-menu {
  background-color: #2a3942;
  border-color: #374248;
}

.dark-theme .dropdown-menu button {
  color: #e1e1e1;
}

.dark-theme .dropdown-menu button:hover {
  background-color: #374248;
}

/* Mobile Header */
.mobile-header {
  display: none;
  padding: 10px 16px;
  background-color: #f0f2f5;
  border-bottom: 1px solid #e9edef;
  align-items: center;
  gap: 15px;
}

.dark-theme .mobile-header {
  background-color: #1f2c33;
  border-color: #2a3942;
}

.mobile-view .mobile-header {
  display: flex;
}

.back-button {
  background: none;
  border: none;
  cursor: pointer;
  color: #54656f;
  padding: 5px;
}

.mobile-header .profile-info {
  display: flex;
  align-items: center;
  gap: 15px;
  flex: 1;
}

.mobile-header .profile-info h3 {
  margin: 0;
  font-size: 16px;
  font-weight: 600;
}

.mobile-header .more-options {
  background: none;
  border: none;
  cursor: pointer;
  color: #54656f;
  padding: 5px;
}

/* Left Panel Styles */
.customer-list-panel {
  width: 350px;
  border-right: 1px solid #e9edef;
  display: flex;
  flex-direction: column;
  background-color: #ffffff;
  overflow: hidden;
}

.mobile-view .customer-list-panel {
  width: 100%;
  height: 100%;
  position: fixed;
  top: 0;
  left: 0;
  z-index: 100;
  transform: translateX(-100%);
  transition: transform 0.3s ease;
}

.mobile-view .customer-list-panel.drawer-open {
  transform: translateX(0);
}

.customer-list-header {
  padding: 10px 16px;
  display: flex;
  align-items: center;
  gap: 10px;
  border-bottom: 1px solid #e9edef;
}

.search-container {
  position: relative;
  flex: 1;
}

.search-input {
  width: 100%;
  padding: 8px 30px 8px 10px;
  border-radius: 18px;
  border: 1px solid #e9edef;
  outline: none;
  font-size: 14px;
  background-color: #f0f2f5;
}

.search-icon {
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  color: #54656f;
}

.theme-toggle {
  background: none;
  border: none;
  cursor: pointer;
  color: #54656f;
  padding: 5px;
}

.customer-list {
  flex: 1;
  overflow-y: auto;
}

.customer-item {
  display: flex;
  align-items: center;
  padding: 12px 16px;
  gap: 12px;
  cursor: pointer;
  border-bottom: 1px solid #e9edef;
  transition: background-color 0.2s;
}

.customer-item:hover {
  background-color: #f5f5f5;
}

.customer-item.active {
  background-color: #e5f3ff;
}

.customer-avatar {
  position: relative;
  width: 50px;
  height: 50px;
  border-radius: 50%;
  overflow: hidden;
  flex-shrink: 0;
}

.customer-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.unread-badge {
  position: absolute;
  top: -5px;
  right: -5px;
  background-color: #25d366;
  color: white;
  border-radius: 50%;
  width: 20px;
  height: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  font-weight: bold;
}

.customer-info {
  flex: 1;
  min-width: 0;
}

.customer-info h4 {
  margin: 0;
  font-size: 16px;
  font-weight: 600;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.last-message {
  margin: 4px 0 0;
  font-size: 13px;
  color: #667781;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.dark-theme .last-message {
  color: #a3b3ba;
}

.message-time {
  font-size: 12px;
  color: #667781;
  white-space: nowrap;
}

.dark-theme .message-time {
  color: #a3b3ba;
}

/* Right Panel Styles */
.chat-area-panel {
  flex: 1;
  display: flex;
  flex-direction: column;
  background-color: #e5ddd5;
  position: relative;
}

.mobile-view .chat-area-panel {
  display: none;
}

.mobile-view .chat-area-panel:has(+ .empty-chat) {
  display: none;
}

.mobile-view .chat-area-panel:not(:has(+ .empty-chat)) {
  display: flex;
}

.empty-chat {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #ffffff;
}

.mobile-view .empty-chat {
  display: none;
}

.empty-content {
  text-align: center;
  max-width: 400px;
  padding: 20px;
}

.empty-content img {
  width: 150px;
  height: 150px;
  margin-bottom: 20px;
  opacity: 0.6;
}

.empty-content h3 {
  margin: 0 0 10px;
  font-size: 20px;
  color: #333;
}

.empty-content p {
  margin: 0;
  color: #667781;
  font-size: 15px;
}

.dark-theme .empty-content h3 {
  color: #e1e1e1;
}

.dark-theme .empty-content p {
  color: #a3b3ba;
}

/* Chat Header */
.chat-header {
  padding: 10px 16px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  background-color: #f0f2f5;
  border-bottom: 1px solid #e9edef;
}

.dark-theme .chat-header {
  background-color: #1f2c33;
  border-color: #2a3942;
}

.profile-info {
  display: flex;
  align-items: center;
  gap: 15px;
}

.profile-info h3 {
  margin: 0;
  font-size: 16px;
  font-weight: 600;
}

.connection-status {
  margin: 2px 0 0;
  font-size: 12px;
}

.connection-status.online {
  color: #25d366;
}

.connection-status.offline {
  color: #667781;
}

.more-options {
  background: none;
  border: none;
  cursor: pointer;
  color: #54656f;
  padding: 5px;
}
.chat-messages {
  flex: 1;
  padding: 16px;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 8px;
  background-image: url('https://web.whatsapp.com/img/bg-chat-tile-light_a4be512e7195b6b733d9110b408f075d.png');
  background-repeat: repeat;
}

.dark-theme .chat-messages {
  background-image: url('https://web.whatsapp.com/img/bg-chat-tile-dark_04fcacde539219fbbad84e5b7225b5a5.png');
}

.date-divider {
  text-align: center;
  margin: 15px 0;
  position: relative;
  color: #667781;
  font-size: 13px;
}

.date-divider::before,
.date-divider::after {
  content: "";
  position: absolute;
  top: 50%;
  width: 40%;
  height: 1px;
  background-color: #e9edef;
}

.date-divider::before {
  left: 0;
}

.date-divider::after {
  right: 0;
}

.dark-theme .date-divider::before,
.dark-theme .date-divider::after {
  background-color: #374248;
}

.message {
  display: flex;
  max-width: 80%;
}

.message.sent {
  align-self: flex-end;
}

.message.received {
  align-self: flex-start;
}

.message-content {
  display: flex;
  gap: 8px;
  align-items: flex-end;
}

.message-avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  overflow: hidden;
  flex-shrink: 0;
}

.message-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.message-bubble {
  padding: 8px 12px;
  border-radius: 8px;
  position: relative;
  word-wrap: break-word;
  max-width: 100%;
}

.message.sent .message-bubble {
  background-color: #d9fdd3;
  border-top-right-radius: 0;
}

.message.received .message-bubble {
  background-color: #ffffff;
  border-top-left-radius: 0;
}

.message-meta {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  gap: 4px;
  margin-top: 4px;
  font-size: 11px;
  color: #667781;
}

.dark-theme .message-meta {
  color: #a3b3ba;
}

.message.sent .message-meta {
  color: #005c4b;
}

.dark-theme .message.sent .message-meta {
  color: #a3b3ba;
}

.deleted-message {
  font-style: italic;
  color: #667781;
}

.message-actions {
  opacity: 0;
  transition: opacity 0.2s;
  margin-left: 8px;
}

.message:hover .message-actions {
  opacity: 1;
}

.dropdown {
  position: relative;
}

.dropdown-toggle {
  background: none;
  border: none;
  cursor: pointer;
  color: #54656f;
  padding: 4px;
}

.dropdown-menu {
  position: absolute;
  right: 0;
  bottom: 100%;
  background-color: white;
  border: 1px solid #e9edef;
  border-radius: 8px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  z-index: 100;
  display: none;
  min-width: 120px;
}

.dropdown:hover .dropdown-menu {
  display: block;
}

.dropdown-menu button {
  display: block;
  width: 100%;
  padding: 8px 12px;
  text-align: left;
  background: none;
  border: none;
  cursor: pointer;
  font-size: 14px;
}

.dropdown-menu button:hover {
  background-color: #f5f5f5;
}

.media-container {
  margin-top: 8px;
}

.media-container img,
.media-container video {
  max-width: 100%;
  max-height: 300px;
  border-radius: 8px;
  cursor: pointer;
}

.file-attachment {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px;
  background-color: #f5f5f5;
  border-radius: 8px;
  text-decoration: none;
  color: inherit;
}

.dark-theme .file-attachment {
  background-color: #374248;
}

.reply-preview {
  border-left: 3px solid #25d366;
  padding-left: 8px;
  margin-bottom: 8px;
  font-size: 13px;
  color: #667781;
  max-width: 100%;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.dark-theme .reply-preview {
  color: #a3b3ba;
}
.status {
  display: inline-flex;
  align-items: center;
}

.single-tick {
  color: #667781;
}

.double-tick {
  color: #4fc3f7;
}

.message-input {
  display: flex;
  align-items: center;
  padding: 8px 16px;
  background-color: #f0f2f5;
  border-top: 1px solid #e9edef;
  position: relative;
}

.dark-theme .message-input {
  background-color: #1f2c33;
  border-color: #2a3942;
}

.attachment-btn,
.send-btn {
  background: none;
  border: none;
  cursor: pointer;
  color: #54656f;
  padding: 8px;
}

.input-wrapper {
  flex: 1;
  position: relative;
  margin: 0 8px;
}

.message-input input {
  width: 100%;
  padding: 10px 12px;
  border: none;
  border-radius: 8px;
  outline: none;
  font-size: 14px;
  background-color: white;
}

.reply-preview-input {
  position: absolute;
  bottom: 100%;
  left: 0;
  right: 0;
  background-color: #f5f5f5;
  padding: 6px 12px;
  border-radius: 8px 8px 0 0;
  font-size: 13px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.dark-theme .reply-preview-input {
  background-color: #2a3942;
}

.reply-preview-input button {
  background: none;
  border: none;
  cursor: pointer;
  color: #54656f;
}

.file-preview {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background-color: #f5f5f5;
  padding: 8px 12px;
  border-radius: 8px;
  margin-top: 5px;
  font-size: 14px;
}

.dark-theme .file-preview {
  background-color: #374248;
}

.file-preview button {
  background: none;
  border: none;
  cursor: pointer;
  color: #54656f;
}

.attachment-menu {
  position: absolute;
  bottom: 100%;
  left: 10px;
  background-color: white;
  border: 1px solid #e9edef;
  border-radius: 8px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  padding: 8px;
  display: flex;
  flex-direction: column;
  gap: 8px;
  min-width: 150px;
}

.dark-theme .attachment-menu {
  background-color: #2a3942;
  border-color: #374248;
}

.attachment-menu button {
  display: flex;
  align-items: center;
  gap: 8px;
  background: none;
  border: none;
  cursor: pointer;
  padding: 6px 8px;
  border-radius: 4px;
  font-size: 14px;
}

.attachment-menu button:hover {
  background-color: #f5f5f5;
}

.dark-theme .attachment-menu button:hover {
  background-color: #374248;
}

.media-viewer {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.9);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.media-content {
  position: relative;
  max-width: 90%;
  max-height: 90%;
}

.media-content img,
.media-content video {
  max-width: 100%;
  max-height: 90vh;
}

.close-viewer {
  position: absolute;
  top: 20px;
  right: 20px;
  background: rgba(0, 0, 0, 0.5);
  border: none;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  display: flex;
  justify-content: center;
  align-items: center;
  cursor: pointer;
  color: white;
}

@media (max-width: 768px) {
  .message {
    max-width: 90%;
  }
  
  .message-bubble {
    padding: 6px 10px;
  }
  
  .message-input {
    padding: 6px 12px;
  }
  
  .message-input input {
    padding: 8px 10px;
  }
  
  .customer-avatar {
    width: 40px;
    height: 40px;
  }
}

@media (max-width: 480px) {
  .customer-info h4 {
    font-size: 14px;
  }
  
  .last-message {
    font-size: 12px;
  }
  
  .message-bubble {
    font-size: 14px;
  }
  
  .message-meta {
    font-size: 10px;
  }
  
  .customer-avatar {
    width: 36px;
    height: 36px;
  }
}


</style>