
import client from "./client";


export const getPhotos=()=>{

return client.get(

"/api/photos"

);

};


export const uploadPhoto=(data)=>{

return client.post(

"/api/photos",

data

);

};
