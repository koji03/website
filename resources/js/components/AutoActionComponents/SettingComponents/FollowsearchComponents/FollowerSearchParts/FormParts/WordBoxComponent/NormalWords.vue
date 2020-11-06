<template>
    <div>
    <p>キーワードがプロフィールに含まれているユーザーのみフォローします。</p>
    <ul class="p-search__box" >
        <li class="p-search__list" v-if="normalList">
            <template v-for="item in normalList" >
                <span class="p-search__list__item" :key="item.index">
                    {{item}}
                    <span>
                        <deleteBtn 
                        v-bind:word="item"
                        v-bind:type="'normal'"
                        />
                    </span>
                </span>
            </template>
        </li>
    </ul>
    </div>

</template>
<script>
import deleteBtn from './deleteBtn'
export default {
    components:{
        deleteBtn
    },
    //キーワードを登録する機能もいいかもしれない。
    computed:{
        normalList () {
           return this.$store.getters['autoActionSetting/getNormal']
      },
    }
}
</script>