<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue'

const props=defineProps({
  sparkColor:{type:String,default:'#fff'},sparkSize:{type:Number,default:10},sparkRadius:{type:Number,default:15},
  sparkCount:{type:Number,default:8},duration:{type:Number,default:400},easing:{type:String,default:'ease-out'},extraScale:{type:Number,default:1},
})
const root=ref(null),canvas=ref(null);let context,observer,frame=0,sparks=[]
const ease=t=>props.easing==='linear'?t:props.easing==='ease-in'?t*t:props.easing==='ease-in-out'?(t<.5?2*t*t:-1+(4-2*t)*t):t*(2-t)
function resize(){if(!root.value||!canvas.value)return;const rect=root.value.getBoundingClientRect(),dpr=Math.min(devicePixelRatio||1,2);canvas.value.width=Math.round(rect.width*dpr);canvas.value.height=Math.round(rect.height*dpr);canvas.value.style.width=`${rect.width}px`;canvas.value.style.height=`${rect.height}px`;context=canvas.value.getContext('2d');context.setTransform(dpr,0,0,dpr,0,0)}
function draw(time){context.clearRect(0,0,canvas.value.clientWidth,canvas.value.clientHeight);sparks=sparks.filter(spark=>{const elapsed=time-spark.start;if(elapsed>=props.duration)return false;const value=ease(elapsed/props.duration),distance=value*props.sparkRadius*props.extraScale,length=props.sparkSize*(1-value),cos=Math.cos(spark.angle),sin=Math.sin(spark.angle);context.globalAlpha=1-value*.55;context.strokeStyle=props.sparkColor;context.lineWidth=2;context.lineCap='round';context.beginPath();context.moveTo(spark.x+distance*cos,spark.y+distance*sin);context.lineTo(spark.x+(distance+length)*cos,spark.y+(distance+length)*sin);context.stroke();return true});context.globalAlpha=1;if(sparks.length)frame=requestAnimationFrame(draw);else frame=0}
function click(event){if(!canvas.value||matchMedia('(prefers-reduced-motion: reduce)').matches)return;const rect=canvas.value.getBoundingClientRect(),x=event.clientX-rect.left,y=event.clientY-rect.top,start=performance.now();sparks.push(...Array.from({length:props.sparkCount},(_,index)=>({x,y,start,angle:Math.PI*2*index/props.sparkCount})));if(!frame)frame=requestAnimationFrame(draw)}
onMounted(()=>{resize();observer=new ResizeObserver(resize);observer.observe(root.value)})
onBeforeUnmount(()=>{observer?.disconnect();cancelAnimationFrame(frame)})
</script>

<template><div ref="root" class="click-spark" @click="click"><canvas ref="canvas" aria-hidden="true"></canvas><slot /></div></template>
<style scoped>.click-spark{position:relative;width:100%;min-height:100vh}.click-spark>canvas{position:absolute;z-index:50;inset:0;display:block;pointer-events:none;user-select:none}</style>
