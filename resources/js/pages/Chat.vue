<template>
  <div class="chat-container" :class="{ 'dark-theme': darkMode }">
    <div class="chat-header">
      <div class="header-content">
        <div class="profile-info">
          <button class="back-button" @click="goBack">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
              <path fill="currentColor" d="M20 11H7.83l5.59-5.59L12 4l-8 8l8 8l1.41-1.41L7.83 13H20z" />
            </svg>
          </button>
          <img src="https://cdn-icons-png.flaticon.com/512/4712/4712035.png" alt="Customer Support"
            class="support-avatar">
          <div>
            <h3>Customer Support</h3>
            <p v-if="isConnected" class="connection-status online">Online</p>
            <p v-else class="connection-status offline">Offline</p>
          </div>
        </div>
        <div class="header-actions">
          <button @click="toggleDarkMode" class="theme-toggle">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
              <path fill="currentColor"
                d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1-8.313-12.454z" />
            </svg>
          </button>
        </div>
      </div>
    </div>
    <div class="chat-messages" ref="messagesContainer">
      <template v-for="(messages, date) in messageGroups" :key="date">
        <div class="date-divider">
          {{ formatDateHeader(date) }}
        </div>
        <div v-for="message in messages" :key="message.id"
          :class="['message', message.sender_type === 'customer' ? 'sent' : 'received']">

          <div class="message-content">
            <div v-if="message.sender_type !== 'customer'" class="message-avatar">
              <img src="https://cdn-icons-png.flaticon.com/512/4712/4712035.png" alt="Support">
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
                    <path fill="currentColor" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <path fill="currentColor" d="M14 2v6h6m-4 5H8m8 4H8m2-8H8" />
                  </svg>
                  Download File
                </a>
              </div>

              <div class="message-meta">
                <span class="time">{{ formatTime(message.created_at) }}</span>
                <span v-if="message.sender_type === 'customer'" class="status">
                  <svg v-if="!message.is_opened" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                    viewBox="0 0 24 24">
                    <path fill="currentColor"
                      d="M18.97 3.659a2.25 2.25 0 0 0-3.182 0l-10.94 10.94a3.75 3.75 0 1 0 5.304 5.303l7.693-7.693a.75.75 0 1 1 1.06 1.06l-7.693 7.693a5.25 5.25 0 1 1-7.424-7.424l10.939-10.94a.75.75 0 1 1 1.061 1.061z" />
                  </svg>
                  <svg v-else xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                    <path fill="#4fc3f7"
                      d="M18.97 3.659a2.25 2.25 0 0 0-3.182 0l-10.94 10.94a3.75 3.75 0 1 0 5.304 5.303l7.693-7.693a.75.75 0 1 1 1.06 1.06l-7.693 7.693a5.25 5.25 0 1 1-7.424-7.424l10.939-10.94a.75.75 0 1 1 1.061 1.061z" />
                    <path fill="#4fc3f7"
                      d="m7.939 14.828l-.396.396a3.75 3.75 0 0 0 5.304 5.304l.396-.396l-5.304-5.304z" />
                  </svg>
                </span>
              </div>
            </div>

            <div v-if="message.sender_type === 'customer'" class="message-actions">
              <div class="dropdown">
                <button class="dropdown-toggle">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                    <path fill="currentColor"
                      d="M12 16a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2m0-6a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2m0-6a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2" />
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

    <!-- Message Input -->
    <div class="message-input">
      <button class="attachment-btn" @click="toggleAttachmentMenu">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
          <path fill="currentColor"
            d="M18 15v3H6v-3H4v3c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2v-3zm-1-4l-1.41-1.41L13 12.17V4h-2v8.17L8.41 9.59L7 11l5 5z" />
        </svg>
      </button>

      <div class="attachment-menu" v-if="showAttachmentMenu">
        <button @click="triggerFileInput('image')">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
            <path fill="currentColor"
              d="M5 21q-.825 0-1.412-.587T3 19V5q0-.825.588-1.412T5 3h14q.825 0 1.413.588T21 5v14q0 .825-.587 1.413T19 21zm0-2h14V5H5v14zm1-2h12l-3.75-5l-3 4L9 13zm-1 2V5v14z" />
          </svg>
          Photo
        </button>
        <button @click="triggerFileInput('video')">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
            <path fill="currentColor"
              d="M17 10.5V7c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1v-3.5l4 4v-11z" />
          </svg>
          Video
        </button>
        <button @click="triggerFileInput('file')">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
            <path fill="currentColor" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
            <path fill="currentColor" d="M14 2v6h6m-4 5H8m8 4H8m2-8H8" />
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
              <path fill="currentColor"
                d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12z" />
            </svg>
          </button>
        </div>

        <div v-if="selectedFile" class="file-preview">
          <span>{{ selectedFile.name }}</span>
          <button @click="removeFile">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
              <path fill="currentColor"
                d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12z" />
            </svg>
          </button>
        </div>
      </div>

      <button class="send-btn" @click="sendMessage" :disabled="!newMessage && !selectedFile">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
          <path fill="currentColor" d="M2.01 21L23 12L2.01 3L2 10l15 2l-15 2z" />
        </svg>
      </button>
    </div>
    <div v-if="showMediaViewer" class="media-viewer" @click="closeMediaViewer">
      <div class="media-content" @click.stop>
        <img v-if="viewingMediaType === 'image'" :src="viewingMediaUrl" alt="Enlarged media">
        <video v-else-if="viewingMediaType === 'video'" controls autoplay>
          <source :src="viewingMediaUrl" type="video/mp4">
        </video>
        <button class="close-viewer" @click="closeMediaViewer">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
            <path fill="currentColor"
              d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12z" />
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { useCustomerStore } from '../stores/customerStore';
import { useCustomerChatBotStore } from '../stores/Chat/CustomerChatStore';
import { onMounted, ref, watch, nextTick, computed } from 'vue';
import { useRouter } from 'vue-router';

export default {
  setup() {
    const customerStore = useCustomerStore();
    const chatStore = useCustomerChatBotStore();
    const router = useRouter();

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

    if (!customerStore.id) {
      router.push('/login');
    }

    onMounted(async () => {
      await chatStore.fetchMessages(customerStore.id);
      chatStore.initializeSocket();
      chatStore.currentCustomerId = customerStore.id;
      scrollToBottom();
    });

    watch(() => chatStore.messages, () => {
      nextTick(() => {
        scrollToBottom();
      });
    }, { deep: true });

    const messageGroups = computed(() => chatStore.messageGroups);

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
          sender_id: customerStore.id,
          message: newMessage.value,
          reply_to: replyingTo.value?.id || null,
          media: selectedFile.value,
          media_type: fileType.value
        };
        if (editingMessage.value != null) {
          await chatStore.updateMessage(editingMessage.value.id,messageData);
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
      const message = chatStore.messages.find(m => m.id === messageId);
      return message ? (message.is_deleted ? 'Deleted message' : message.message) : 'Original message not found';
    };

    const goBack = () => {
      router.go(-1);
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
      messageGroups,
      isConnected: computed(() => chatStore.isConnected),
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
      goBack
    };
  }
};
</script>

<style scoped>
.chat-container {
  width: 100%;
  max-width: 100%;
  height: 100vh;
  display: flex;
  flex-direction: column;
  background-color: #f0f2f5;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  transition: background-color 0.3s ease;
}

.dark-theme {
  background-color: #0a1014;
  color: #e1e1e1;
}

.dark-theme .chat-header,
.dark-theme .message-input {
  background-color: #1f2c33;
  border-color: #2a3942;
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

.chat-header {
  background-color: #f0f2f5;
  padding: 10px 16px;
  border-bottom: 1px solid #e9edef;
  position: relative;
  z-index: 10;
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.profile-info {
  display: flex;
  align-items: center;
  gap: 15px;
}

.back-button {
  background: none;
  border: none;
  cursor: pointer;
  margin-right: 10px;
  color: inherit;
}

.support-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
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

.header-actions {
  display: flex;
  gap: 15px;
}

.theme-toggle,
.search-toggle {
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

.message-input {
  display: flex;
  align-items: center;
  padding: 8px 16px;
  background-color: #f0f2f5;
  border-top: 1px solid #e9edef;
  position: relative;
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
  .chat-container {
    height: 100vh;
  }

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
}

@media (max-width: 480px) {
  .profile-info h3 {
    font-size: 14px;
  }

  .connection-status {
    font-size: 11px;
  }

  .message-bubble {
    font-size: 14px;
  }

  .message-meta {
    font-size: 10px;
  }
}
</style>