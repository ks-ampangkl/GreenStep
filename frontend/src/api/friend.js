
import client from "./client";


export const getFriends=()=>{

return client.get(

"/api/friends"

);

};


export const getRequests=()=>{

return client.get(

"/api/friends/requests"

);

};


export const sendRequest=(data)=>{

return client.post(

"/api/friends/request",

data

);

};


export const acceptRequest=(id)=>{

return client.post(

`/api/friends/accept/${id}`

);

};


export const rejectRequest=(id)=>{

return client.post(

`/api/friends/reject/${id}`

);

};
