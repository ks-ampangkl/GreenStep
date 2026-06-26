<template>
  <h1>Friend Requests</h1>

  <div v-if="error" class="error-banner">{{ error }}</div>
  <div v-if="message" class="success-banner">{{ message }}</div>
  <div v-if="loading" class="loading-state">Loading requests...</div>

  <div v-else-if="requests.length">
    <div v-for="r in requests" :key="r.id" class="card card-row">
      <div style="display:flex; align-items:center; gap:12px;">
        <div class="avatar">{{ initials(r.name) }}</div>
        <div>
          <h3>{{ r.name }}</h3>
          <p class="hint-text">{{ r.requested_at }}</p>
        </div>
      </div>
      <div class="card-actions" style="margin-top:0;">
        <button class="btn-sm" @click="accept(r.id)">Accept</button>
        <button class="btn-danger btn-sm" @click="reject(r.id)">Reject</button>
      </div>
    </div>
  </div>

  <div v-else class="empty-state">
    <div class="empty-icon">📭</div>
    No pending requests right now.
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { getRequests, acceptRequest, rejectRequest } from "../api/friend";
import { apiErrorMessage } from "../api/client";

const requests = ref([]);
const loading = ref(true);
const error = ref("");
const message = ref("");

function initials(name) {
  return (name || "")
    .split(" ")
    .map((p) => p[0])
    .join("")
    .slice(0, 2)
    .toUpperCase();
}

async function load() {
  loading.value = true;
  error.value = "";
  try {
    const res = await getRequests();
    requests.value = res.data.pending_requests ?? [];
  } catch (err) {
    error.value = apiErrorMessage(err, "Could not load friend requests.");
  } finally {
    loading.value = false;
  }
}

async function accept(id) {
  error.value = "";
  message.value = "";
  try {
    await acceptRequest(id);
    message.value = "Friend request accepted.";
    load();
  } catch (err) {
    error.value = apiErrorMessage(err, "Could not accept this request.");
  }
}

async function reject(id) {
  error.value = "";
  message.value = "";
  try {
    await rejectRequest(id);
    message.value = "Friend request rejected.";
    load();
  } catch (err) {
    error.value = apiErrorMessage(err, "Could not reject this request.");
  }
}

onMounted(load);
</script>
