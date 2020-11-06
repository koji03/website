<template>
    <div class="p-data__box">
        <div class="p-data__box__title p-data__box__title--border">
            ターゲットリスト
        </div>
        <Loading v-if="targetListFormLoad"/>
        <div v-else class="p-data__box__main u-padding__data">
            <ul class="p-data__box__list">
            <li class="u-margin__data--min" v-for="target in targetList" :key="target.index">
                {{ target }}
            </li>
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
            targetList:[]
        }
    },
    components:{
        Loading
    },
    computed:{
        ...mapState({
            targetListFormLoad: state => state.autoActionSetting.targetListFormLoad,
        }),
    },
    methods:{
        //dbから表示させて画面に表示させる。
        async getTargetList(){
        this.targetList = []
            const response = await axios.post('/api/getTargetList',{
                twitter_screen_name:this.$route.params.screen_name
            });
            this.$store.commit('autoActionSetting/setTargetListFormLoad',false)
            if(response.status !== OK){
                this.$store.commit('error/setCode',response.status)
                return false
            }
            for(let item in response.data){
                this.targetList.push(response.data[item].target)
            }
            this.$store.commit('autoActionSetting/setTargetList',this.targetList)
        },
    },
    created(){
        this.getTargetList()
    },
    watch:{
        '$route':async function(){
            this.getTargetList()
        }
    }
}
</script>