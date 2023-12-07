<template>
    <div>
        <div class="share-buttons">
            <iframe width="560" height="315" src="https://www.youtube.com/embed/Q7AOvWpIVHU?si=7bCORyxd2SZkcfri" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
            <p>
                <button type="button" class="btn btn-primary" @click="shareOnFacebook">Share on Facebook</button>
                <button @click="shareOnTikTok">Share on TikTok</button>
            </p>
            <ul v-if="data">
                <a :href="data">Share FaceBook</a>
            </ul>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    name: "Example",
    data() {
      return {
          data: null
      }
    },
    methods: {
        shareOnFacebook() {
            axios.post(`/api/share-video/${923279822752038}`)
                .then(response => {
                    this.data = response.data;
                    console.log("Response from server:", response.data);
                })
                .catch(error => {
                    console.error("Error:", error);
                });
        },
        shareOnTikTok() {
        },
    },
};
</script>

<style scoped>

.share-buttons {
    text-align: center;
    margin-top: 20px;
}

button {
    font-size: 24px;
    margin: 0 10px;
    padding: 10px;
    cursor: pointer;
    color: #ffffff; /* Màu chữ trắng */
    background-color: #00b8ff; /* Màu nền là màu xanh lá cây */
    border: none;
    border-radius: 5px;
}
</style>
