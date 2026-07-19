<script setup>
import { nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue'

const props = defineProps({
  items:{type:Array,default:()=>[]},showGradients:{type:Boolean,default:true},
  enableArrowNavigation:{type:Boolean,default:true},displayScrollbar:{type:Boolean,default:true},
  initialSelectedIndex:{type:Number,default:-1},itemKey:{type:[String,Function],default:'id'},
})
const emit=defineEmits(['select'])
const list=ref(null),selectedIndex=ref(props.initialSelectedIndex),topOpacity=ref(0),bottomOpacity=ref(0),visibleKeys=ref(new Set())
let observer
const keyFor=(item,index)=>typeof props.itemKey==='function'?props.itemKey(item,index):item?.[props.itemKey]??index
const domKeyFor=(item,index)=>String(keyFor(item,index))
function scrollState(){if(!list.value)return;const{scrollTop,scrollHeight,clientHeight}=list.value;topOpacity.value=Math.min(scrollTop/50,1);bottomOpacity.value=scrollHeight<=clientHeight?0:Math.min((scrollHeight-scrollTop-clientHeight)/50,1)}
function choose(index,notify=false){if(!props.items.length)return;selectedIndex.value=Math.max(0,Math.min(index,props.items.length-1));nextTick(()=>list.value?.querySelector(`[data-index="${selectedIndex.value}"]`)?.scrollIntoView({block:'nearest',behavior:'smooth'}));if(notify)emit('select',props.items[selectedIndex.value],selectedIndex.value)}
function keys(event){if(!props.enableArrowNavigation||!list.value?.contains(document.activeElement))return;if(event.key==='ArrowDown'||(event.key==='Tab'&&!event.shiftKey)){event.preventDefault();choose(selectedIndex.value+1)}else if(event.key==='ArrowUp'||(event.key==='Tab'&&event.shiftKey)){event.preventDefault();choose(selectedIndex.value<0?0:selectedIndex.value-1)}else if(event.key==='Enter'&&selectedIndex.value>=0){event.preventDefault();choose(selectedIndex.value,true)}}
function observe(){observer?.disconnect();observer=new IntersectionObserver(entries=>entries.forEach(entry=>{if(entry.isIntersecting){const key=entry.target.dataset.key;if(key!==undefined&&!visibleKeys.value.has(key)){visibleKeys.value=new Set([...visibleKeys.value,key])}observer?.unobserve(entry.target)}}),{root:list.value,threshold:.2});list.value?.querySelectorAll('.animated-row').forEach(row=>{if(!visibleKeys.value.has(row.dataset.key))observer.observe(row)});nextTick(scrollState)}
watch(()=>props.items,()=>nextTick(observe),{deep:true})
onMounted(()=>{observe();window.addEventListener('keydown',keys)})
onBeforeUnmount(()=>{observer?.disconnect();window.removeEventListener('keydown',keys)})
</script>

<template>
  <div class="animated-list-wrap">
    <ul ref="list" class="animated-list" :class="{ 'no-scrollbar':!displayScrollbar }" tabindex="0" aria-label="Görev listesi" @scroll="scrollState" @mouseleave="selectedIndex=-1">
      <li v-for="(item,index) in items" :key="keyFor(item,index)" class="animated-row" :class="{visible:visibleKeys.has(domKeyFor(item,index)),selected:selectedIndex===index}" :data-index="index" :data-key="domKeyFor(item,index)" @mouseenter="selectedIndex=index" @click.self="choose(index,true)">
        <slot :item="item" :index="index" :selected="selectedIndex===index">{{ item }}</slot>
      </li>
    </ul>
    <div v-if="showGradients" class="top-fade" :style="{opacity:topOpacity}"></div>
    <div v-if="showGradients" class="bottom-fade" :style="{opacity:bottomOpacity}"></div>
  </div>
</template>

<style scoped>
.animated-list-wrap{position:relative;min-width:0}.animated-list{display:flex;flex-direction:column;gap:9px;max-height:420px;margin:0;padding:2px 5px 2px 2px;overflow-y:auto;overflow-x:hidden;list-style:none;outline:none;scrollbar-color:var(--blue) var(--control);scrollbar-width:thin}.animated-list::-webkit-scrollbar{width:7px}.animated-list::-webkit-scrollbar-track{background:var(--control);border-radius:6px}.animated-list::-webkit-scrollbar-thumb{background:var(--blue);border-radius:6px}.animated-list.no-scrollbar{scrollbar-width:none}.animated-list.no-scrollbar::-webkit-scrollbar{display:none}.animated-row{opacity:0;border-radius:14px;transform:scale(.92) translateY(8px);transition:opacity .24s ease,transform .24s ease,box-shadow .18s ease}.animated-row.visible{opacity:1;transform:scale(1) translateY(0)}.animated-row.selected{box-shadow:0 0 0 1px var(--border-strong)}.top-fade,.bottom-fade{position:absolute;z-index:3;left:0;right:6px;pointer-events:none;transition:opacity .3s ease}.top-fade{top:0;height:44px;background:linear-gradient(to bottom,var(--fade-color) 5%,transparent)}.bottom-fade{bottom:0;height:72px;background:linear-gradient(to top,var(--fade-color) 5%,transparent)}
@media(prefers-reduced-motion:reduce){.animated-row{opacity:1;transform:none;transition:none}}
</style>
