
import client from "./client";


export const getActivityTypes=()=>{

return client.get(

"/api/activities/types"

);

};


export const getTodayLogs=()=>{

return client.get(

"/api/activities/today"

);

};


export const getHistory=()=>{

return client.get(

"/api/activities/history"

);

};


export const logActivity=(data)=>{

return client.post(

"/api/activities/log",

data

);

};
