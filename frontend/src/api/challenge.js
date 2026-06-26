
import client from "./client";


export const getChallenges=(filter = "all")=>{

return client.get(

"/api/challenges",

{ params: { filter } }

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
