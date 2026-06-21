<template>
  <div class="page-head">
    <div>
      <span class="eyebrow">{{ challenges.length }} active</span>
      <h1>Challenges</h1>
    </div>
    <router-link to="/challenges/new" class="btn">
      <svg viewBox="0 0 20 20" width="14" height="14" fill="none"><path d="M10 4v12M4 10h12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
      Create Challenge
    </router-link>
  </div>

  <div v-if="challenges.length">
    <div v-for="c in challenges" :key="c.id" class="card">
      <div class="card-row">
        <h3>{{ c.title }}</h3>
        <span class="tag">{{ c.target_type }}</span>
      </div>
      <p style="color: var(--color-text-muted); font-size: 13.5px; margin: 6px 0 0;">{{ c.description }}</p>

      <div class="card-actions">
        <button class="btn-sm" @click="join(c.id)">Join</button>
        <button class="btn-danger btn-sm" @click="remove(c.id)">Delete</button>
      </div>
    </div>
  </div>

  <div v-else class="empty-state">
    <div class="empty-icon">🚩</div>
    No challenges yet — start one and invite your friends.
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { getChallenges, joinChallenge, deleteChallenge } from "../api/challenge";

const challenges = ref([]);

async function load() {
  const res = await getChallenges();
  challenges.value = res.data;
}

async function join(id) {
  await joinChallenge(id);
  alert("Joined");
}

async function remove(id) {
  await deleteChallenge(id);
  await load();
}

onMounted(load);
</script>
