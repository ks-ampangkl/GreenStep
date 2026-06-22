import { createRouter, createWebHistory } from "vue-router";

import Login from "../views/Login.vue";
import Register from "../views/Register.vue";
import Dashboard from "../views/Dashboard.vue";
import ActivityList from "../views/ActivityList.vue";
import LogActivity from "../views/LogActivity.vue";
import ChallengeList from "../views/ChallengeList.vue";
import ChallengeCreate from "../views/ChallengeCreate.vue";
import FriendList from "../views/FriendList.vue";
import FriendRequests from "../views/FriendRequests.vue";
import Goal from "../views/Goal.vue";
import PhotoGallery from "../views/PhotoGallery.vue";
import Leaderboard from "../views/Leaderboard.vue";

const routes = [
  { path: "/", component: Dashboard },
  { path: "/login", component: Login, meta: { hideLayout: true } },
  { path: "/register", component: Register, meta: { hideLayout: true } },
  { path: "/activities", component: ActivityList },
  { path: "/activities/log", component: LogActivity },
  { path: "/challenges", component: ChallengeList },
  { path: "/challenges/new", component: ChallengeCreate },
  { path: "/friends", component: FriendList },
  { path: "/friends/requests", component: FriendRequests },
  { path: "/goals", component: Goal },
  { path: "/photos", component: PhotoGallery },
  { path: "/leaderboard", component: Leaderboard },
];

export default createRouter({
  history: createWebHistory(),
  routes,
});
