
import client from "./client";


export const getChallenges=()=>{

return client.get(

"/api/challenges"

);

};


export const createChallenge=(data)=>{

return client.post(

"/api/challenges",

data

);

};


export const deleteChallenge=(id)=>{

return client.delete(

`/api/challenges/${id}`

);

};


export const joinChallenge=(id)=>{

return client.post(

`/api/challenges/${id}/join`

);

};
