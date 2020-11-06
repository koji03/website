<template>
    <div class="p-data__box">
        <div class="p-data__box__title">
            いいね
        </div>
        <Loading v-if="likeLoad"/>
        <div v-else class="p-data__box__main u-padding__data">
            <ul class="p-data__box__list">
            <div class="p-data__text--bold p-data__text">必須ワード</div>
                <li class="u-margin__data--min" v-for="word in like.normal" :key="word.index" >{{word}}</li>
            </ul>
            <ul class="p-data__box__list"> 
                <div class="p-data__text--bold p-data__text">ORワード</div>
                <li class="u-margin__data--min" v-for="word in like.or" :key="word.index">{{word}}</li>
            </ul>
            <ul class="p-data__box__list">
                <div class="p-data__text--bold p-data__text">NGワード</div>
                <li class="u-margin__data--min" v-for="word in like.ng" :key="word.index">{{word}}</li>
            </ul>
        </div>

    </div>
</template>
<script>
import { mapState } from 'vuex'
import Loading from '../../Loading.vue'
import { OK } from '../../../util'

export default {
    data(){
        return{
            like:{
                normal:[],
                or:[],
                ng:[],
            },
        }
    },
    components:{
        Loading
    },
    computed:{
        ...mapState({
            likeLoad: state => state.twitter.likeLoad,
        }),
    },
    methods:{
        //dbから取得して表示させる。
        async getLikeWordList(){
            this.$store.commit('autoActionSetting/ResetLikeWordList')
            
            const response = await axios.post('/api/getLikeWordList',{
                twitter_screen_name:this.$route.params.screen_name
            });
            this.$store.commit('autoActionSetting/setLikeLoad',true)
            if(response.status !== OK){
                this.$store.commit('error/setCode',response.status)
                return false
            }
            this.like = {
                normal:[],
                or:[],
                ng:[],
            }
            await response.data.forEach(element => {
                this.$store.dispatch('autoActionSetting/setLikeWordList',element)
                for(let i in element){
                    if(element[i].length <=0){
                            continue;
                        }
                        if(i == 'or'){
                            this.like.or = element[i]
                        }
                        if(i == 'ng'){
                            this.like.ng = element[i]
                        }
                        if(i == 'normal'){
                            this.like.normal= element[i]
                        }
                    }
            });
            this.$store.commit('autoActionSetting/setLikeLoad',false)
        },
    },
    created(){
        this.getLikeWordList()
    },
    watch:{
        '$route':async function(){
            this.getLikeWordList()
        }
    }
}
</script>