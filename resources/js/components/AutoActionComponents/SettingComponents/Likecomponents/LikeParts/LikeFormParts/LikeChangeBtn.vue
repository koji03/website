<template>
    <div class="p-search__btn">
        <span class="p-search__btn__item" 
        @click="changeForm('normal')" 
        v-bind:class="{ js_search__btn__color: active.normal }">
        必須ワード
        </span>
        <span class="p-search__btn__item" 
        @click="changeForm('or')"
        v-bind:class="{ js_search__btn__color: active.or }">ORワード</span>
        <span class="p-search__btn__item" 
        @click="changeForm('ng')"
        v-bind:class="{ js_search__btn__color: active.ng }">NGワード</span>
    </div>
</template>
<script>
export default {
    data(){
        return{
            active:{
                normal:null,    
                or:null,
                ng:null,
            }
        }
    },
    methods:{
        //formを変える。autoActionSettingに格納する。
        changeForm(data){
            this.$store.commit('autoActionSetting/currentLikeForm',data)
        },
        //表示しているformのボタンの背景色をかえる
        changeActiveClass(data){
            for(let k in this.active) {
                if(k == data){
                    this.active[k] = true
                }
                else{
                    this.active[k] = false
                }
            }
        }
    },        
    computed:{
        getForm:function(){
            return this.$store.state.autoActionSetting.currentLikeForm
        }
    },        
    watch:{
        //formが変わったら現在のformを取得
        getForm(){
            if(this.getForm === 'normal'){
                this.changeActiveClass('normal')
            }else if(this.getForm === 'or'){
                this.changeActiveClass('or')
            }else if(this.getForm === 'ng'){
                this.changeActiveClass('ng')
            }
        }
    },
    created(){
        this.active.normal = true
        this.$store.commit('autoActionSetting/currentLikeForm','normal')
    }
}
</script>