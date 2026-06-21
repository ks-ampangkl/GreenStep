<template>
  <h1>Friend Requests</h1>

  <div v-if="requests.length">
    <div v-for="r in requests" :key="r.id" class="card card-row">
      <div style="display:flex; align-items:center; gap:12px;">
        <div class="avatar">{{ initials(r.sender_name) }}</div>
        <h3>{{ r.sender_name }}</h3>
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

const requests = ref([]);

function initials(name) {
  return (name || "")
    .split(" ")
    .map((p) => p[0])
    .join("")
    .slice(0, 2)
    .toUpperCase();
}

async function load() {
  const res = await getRequests();
  requests.value = res.data.pending_requests;
}

async function accept(id) {
  await acceptRequest(id);
  load();
}

async function reject(id) {
  await rejectRequest(id);
  load();
}

onMounted(load);
</script>
