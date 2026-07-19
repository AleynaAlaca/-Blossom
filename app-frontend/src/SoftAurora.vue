<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue'
import { Mesh, Program, Renderer, Triangle } from 'ogl'

const props = defineProps({
  speed:{type:Number,default:.6},scale:{type:Number,default:1.5},brightness:{type:Number,default:1},
  color1:{type:String,default:'#f7f7f7'},color2:{type:String,default:'#e100ff'},
  enableMouseInteraction:{type:Boolean,default:true},mouseInfluence:{type:Number,default:.25},
})
const host=ref(null);let gl,renderer,program,frame;let mouse=[.5,.5],target=[.5,.5]
const vertex=`attribute vec2 position;void main(){gl_Position=vec4(position,0.,1.);}`
const fragment=`precision highp float;uniform float uTime,uSpeed,uScale,uBrightness,uMouseInfluence;uniform vec2 uResolution,uMouse;uniform vec3 uColor1,uColor2;
float hash(vec2 p){return fract(sin(dot(p,vec2(127.1,311.7)))*43758.5453);}
float noise(vec2 p){vec2 i=floor(p),f=fract(p);f=f*f*(3.-2.*f);return mix(mix(hash(i),hash(i+vec2(1,0)),f.x),mix(hash(i+vec2(0,1)),hash(i+1.),f.x),f.y);}
float fbm(vec2 p){float n=0.,a=.5;for(int i=0;i<5;i++){n+=a*noise(p);p=p*2.03+17.1;a*=.48;}return n;}
void main(){vec2 uv=gl_FragCoord.xy/uResolution.xy;vec2 p=(uv-.5)*vec2(uResolution.x/uResolution.y,1.);p+=(uMouse-.5)*uMouseInfluence;float t=uTime*uSpeed*.12;float n=fbm(vec2(p.x*uScale+t,p.y*uScale-t*.45));float wave=.5+.10*sin(p.x*4.+t*7.)+.14*(n-.5);float band=exp(-18.*abs(p.y-wave+.42));float band2=exp(-13.*abs(p.y-wave+.58));vec3 col=uColor1*band*(.65+.45*n)+uColor2*band2*(.48+.52*(1.-n));float glow=clamp(length(col)*uBrightness,0.,1.);gl_FragColor=vec4(col*uBrightness,glow*.82);}`
const color=(hex)=>[0,2,4].map(i=>parseInt(hex.replace('#','').slice(i,i+2),16)/255)
function resize(){if(!host.value)return;renderer.setSize(host.value.offsetWidth,host.value.offsetHeight);program.uniforms.uResolution.value=[gl.canvas.width,gl.canvas.height]}
function move(e){target=[e.clientX/window.innerWidth,1-e.clientY/window.innerHeight]}
onMounted(()=>{renderer=new Renderer({alpha:true,dpr:Math.min(devicePixelRatio,2)});gl=renderer.gl;gl.clearColor(0,0,0,0);program=new Program(gl,{vertex,fragment,transparent:true,uniforms:{uTime:{value:0},uSpeed:{value:props.speed},uScale:{value:props.scale},uBrightness:{value:props.brightness},uResolution:{value:[1,1]},uColor1:{value:color(props.color1)},uColor2:{value:color(props.color2)},uMouse:{value:new Float32Array(mouse)},uMouseInfluence:{value:props.enableMouseInteraction?props.mouseInfluence:0}}});const mesh=new Mesh(gl,{geometry:new Triangle(gl),program});host.value.appendChild(gl.canvas);resize();const draw=t=>{frame=requestAnimationFrame(draw);program.uniforms.uTime.value=t*.001;mouse[0]+=.04*(target[0]-mouse[0]);mouse[1]+=.04*(target[1]-mouse[1]);program.uniforms.uMouse.value.set(mouse);renderer.render({scene:mesh})};window.addEventListener('resize',resize);if(props.enableMouseInteraction)window.addEventListener('mousemove',move);frame=requestAnimationFrame(draw)})
onBeforeUnmount(()=>{cancelAnimationFrame(frame);window.removeEventListener('resize',resize);window.removeEventListener('mousemove',move);gl?.getExtension('WEBGL_lose_context')?.loseContext();gl?.canvas.remove()})
</script>
<template><div ref="host" class="soft-aurora" aria-hidden="true"></div></template>
<style scoped>.soft-aurora{position:absolute;inset:0;overflow:hidden}.soft-aurora :deep(canvas){display:block;width:100%;height:100%}</style>
