<!-- Bootstrap CDN -->
<script src="js/jquery.min.js" type="text/javascript"></script>
<script src="js/popper.min.js" type="text/javascript"></script>
<script src="js/bootstrap.min.js"></script>
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>


<!-- Bootstrap CDN -->

<!-- Main Js -->
<script src="js/main.js"></script>
<!-- Main Js -->

<!-- Slick Slider CDN -->
<script src="slick/slick.min.js"></script>
<!-- Slick Slider CDN -->

<!-- Wow Js CDN -->
<script src="js/wow.min.js"></script>
<!-- AOS ANIMTAION CDN -->
<script>
new WOW().init();
AOS.init();
</script>

</script>

<script>
// === GSAP & ScrollTrigger ===
gsap.registerPlugin(ScrollTrigger);

// Batch fade-in for sections
gsap.utils.toArray(".sec-title, .service-box").forEach((el, i) => {
  gsap.fromTo(el,
    { opacity: 0, y: 50 },
    {
      opacity: 1,
      y: 0,
      duration: el.classList.contains("sec-title") ? 1.2 : 1,
      delay: el.classList.contains("service-box") ? i * 0.15 : 0,
      ease: "power3.out",
      scrollTrigger: { trigger: el, start: "top 90%" }
    }
  );
});

// === 3D Tilt Effect - Throttled ===
const tiltCards = document.querySelectorAll(".service-box, .glass-card");
tiltCards.forEach(card => {
  let requestId;
  card.addEventListener("mousemove", e => {
    if (requestId) cancelAnimationFrame(requestId);
    requestId = requestAnimationFrame(() => {
      const rect = card.getBoundingClientRect();
      const x = (e.clientX - rect.left - rect.width/2) / (rect.width/2);
      const y = (e.clientY - rect.top - rect.height/2) / (rect.height/2);
      card.style.transform = `perspective(1000px) rotateX(${-y*10}deg) rotateY(${x*15}deg) translateY(-10px) scale(1.05)`;
    });
  });
  card.addEventListener("mouseleave", () => {
    card.style.transform = "perspective(1000px) rotateX(0) rotateY(0) translateY(0) scale(1)";
  });
});

// === Three.js Particle System - Reduced ===
const canvas = document.getElementById("hero-canvas");
let scene, camera, renderer, particles, particleData=[], particleCount = 300; // reduce particles
function initThree() {
  scene = new THREE.Scene();
  camera = new THREE.PerspectiveCamera(75, window.innerWidth/window.innerHeight, 0.1, 1000);
  renderer = new THREE.WebGLRenderer({ canvas, alpha: true, antialias: true });
  renderer.setSize(window.innerWidth, window.innerHeight);

  const geometry = new THREE.BufferGeometry();
  const positions = [], colors = [];
  const color1 = new THREE.Color(0x9D4CFF), color2 = new THREE.Color(0x0080ff);

  for (let i=0;i<particleCount;i++) {
    const x=(Math.random()-0.5)*100, y=(Math.random()-0.5)*100, z=(Math.random()-0.5)*100;
    positions.push(x,y,z);
    const color = color1.clone().lerp(color2, Math.random());
    colors.push(color.r,color.g,color.b);
    particleData.push({ velocity: new THREE.Vector3((Math.random()-0.5)*0.01, (Math.random()-0.5)*0.01, (Math.random()-0.5)*0.01) });
  }

  geometry.setAttribute('position', new THREE.Float32BufferAttribute(positions,3));
  geometry.setAttribute('color', new THREE.Float32BufferAttribute(colors,3));

  const material = new THREE.PointsMaterial({ size:0.5, vertexColors:true, transparent:true, opacity:0.8, blending: THREE.AdditiveBlending });
  particles = new THREE.Points(geometry, material);
  scene.add(particles);
  camera.position.z = 50;
}

function animateThree() {
  requestAnimationFrame(animateThree);
  const pos = particles.geometry.attributes.position.array;
  for(let i=0;i<particleCount;i++){
    const i3=i*3, data=particleData[i];
    pos[i3]+=data.velocity.x; pos[i3+1]+=data.velocity.y; pos[i3+2]+=data.velocity.z;
    if(Math.abs(pos[i3])>50) pos[i3]*=-1;
    if(Math.abs(pos[i3+1])>50) pos[i3+1]*=-1;
    if(Math.abs(pos[i3+2])>50) pos[i3+2]*=-1;
  }
  particles.geometry.attributes.position.needsUpdate=true;
  particles.rotation.y += 0.0005;
  particles.rotation.x += 0.0002;
  renderer.render(scene,camera);
}

window.addEventListener("resize", ()=>{
  camera.aspect=window.innerWidth/window.innerHeight;
  camera.updateProjectionMatrix();
  renderer.setSize(window.innerWidth, window.innerHeight);
});

// ScrollTrigger for Hero
function initHeroScroll() {
  gsap.to(particles.rotation, {
    scrollTrigger: { trigger: "#hero", start: "top top", end: "bottom top", scrub: 1 },
    z: Math.PI*2, y: Math.PI/4, ease: "none"
  });
  gsap.to(camera.position, {
    scrollTrigger: { trigger: "#hero", start: "top top", end: "bottom top", scrub: 1 },
    z: 80, ease: "none"
  });
}

window.addEventListener("load", ()=>{
  if(typeof THREE!=="undefined" && typeof gsap!=="undefined") {
    initThree();
    animateThree();
    initHeroScroll();
  }
});
// --- 8. Circle Counters ---
document.querySelectorAll(".circle-box").forEach(box=>{
  const target=parseInt(box.dataset.target),
        numberEl=box.querySelector(".counter-number"),
        circle=box.querySelector(".circle-progress"),
        radius=50,circumference=2*Math.PI*radius;
  const colorType=box.dataset.color;
  circle.style.stroke=colorType==="cyan"?"var(--cyan-glow)":colorType==="purple"?"var(--purple-glow)":"var(--pink-neon)";
  circle.style.filter=`drop-shadow(0 0 12px ${colorType==="cyan"?"var(--cyan-glow)":colorType==="purple"?"var(--purple-glow)":"var(--pink-neon)"})`;
  gsap.set(circle,{strokeDasharray:circumference,strokeDashoffset:circumference});
  ScrollTrigger.create({trigger:box,start:"top 80%",toggleActions:"restart none restart none",onEnter:animate,onEnterBack:animate,onLeave:reset,onLeaveBack:reset});
  function animate(){let c={val:0};gsap.to(c,{val:target,duration:2,ease:"power2.out",onUpdate:()=>{numberEl.textContent=Math.round(c.val)+"%";circle.style.strokeDashoffset=circumference-(c.val/100)*circumference;}});}
  function reset(){numberEl.textContent="0%";circle.style.strokeDashoffset=circumference;}
});
// === Slick Slider - Keep as is ===
$('.review_slider').slick({
  dots: true,
  arrows: false,
  infinite: true,
  speed: 700,
  slidesToShow: 3,
  slidesToScroll: 1,
  autoplay: true,
  autoplaySpeed: 2500,
  pauseOnHover: true,
  cssEase: "ease-in-out",
  responsive: [{ breakpoint: 600, settings: { slidesToShow: 1, slidesToScroll: 1 }}]
});

// === Navigation toggle optimized ===
const navToggle = document.querySelector('.nav-toggle');
const navMenu = document.querySelector('.nav-menu');
const navLinks = document.querySelectorAll('.nav-menu li a');

navToggle.addEventListener('click', () => {
  navToggle.classList.toggle('is-open');
  navMenu.classList.toggle('is-open');

  if(navMenu.classList.contains('is-open')) {
    gsap.fromTo(navLinks, { opacity:0, y:20 }, { opacity:1, y:0, stagger:0.1, duration:0.4, ease:"power2.out" });
  }
});

navLinks.forEach(link=>{
  link.addEventListener('click', ()=>{
    navToggle.classList.remove('is-open');
    navMenu.classList.remove('is-open');
  });
});
</script>
