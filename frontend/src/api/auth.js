
import client from "./client";


export const login=(data)=>{

return client.post(

"/auth/login",

data

);

};


export const register=(data)=>{

return client.post(

"/auth/register",

data

);

};


export const me=()=>{

return client.get(

"/auth/me"

);

};
