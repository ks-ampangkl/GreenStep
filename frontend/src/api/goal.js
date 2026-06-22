
import client from "./client";


export const getGoal=()=>{

return client.get(

"/api/goals"

);

};


export const setGoal=(data)=>{

return client.post(

"/api/goals",

data

);

};
