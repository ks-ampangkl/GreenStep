<template>
  <div class="page-head">
    <div>
      <span class="eyebrow">{{ friends.length }} connected</span>
      <h1>Friends</h1>
    </div>
    <router-link to="/friends/requests" class="btn-ghost btn">Requests</router-link>
  </div>

  <div v-if="friends.length">
    <div v-for="f in friends" :key="f.id" class="card card-row">
      <div style="display:flex; align-items:center; gap:12px;">
        <div class="avatar">{{ initials(f.name) }}</div>
        <div>
          <h3 style="margin-bottom:2px;">{{ f.name }}</h3>
          <span style="color: var(--color-text-muted); font-size: 13px;">{{ f.email }}</span>
        </div>
      </div>
    </div>
  </div>

  <div v-else class="empty-state">
    <div class="empty-icon">🤝</div>
    No friends yet — send a request to start tracking together.
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { getFriends } from "../api/friend";

const friends = ref([]);

function initials(name) {
  return (name || "")
    .split(" ")
    .map((p) => p[0])
    .join("")
    .slice(0, 2)
    .toUpperCase();
}

onMounted(async () => {
  const res = await getFriends();
  friends.value = res.data.friends;
});
</script>
