
import client from "./client";

export const getDashboard = () => {
  return client.get("/api/dashboard");
};

export const getActivityTypes = () => {
  return client.get("/api/activities/types");
};

export const getTodayLogs = () => {
  return client.get("/api/activities/today");
};

export const getHistory = () => {
  return client.get("/api/activities/history");
};

export const logActivity = (data) => {
  return client.post("/api/activities/log", data);
};

export const getLeaderboard = () => {
  return client.get("/api/leaderboard");
};

export const getTips = (category = "all") => {
  return client.get("/api/tips", { params: { category } });
};

export const resetDemoData = () => {
  return client.post("/api/reset");
};
