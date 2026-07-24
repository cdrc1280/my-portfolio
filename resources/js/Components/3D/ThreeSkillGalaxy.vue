<template>
  <div class="relative w-full h-[400px] sm:h-[550px] flex items-center justify-center">
    <div ref="mountContainer" class="w-full h-full cursor-grab active:cursor-grabbing"></div>
    
    <!-- Hover telemetry badge -->
    <div 
      v-if="hoveredSkill" 
      class="absolute bottom-4 sm:bottom-6 left-1/2 -translate-x-1/2 glass-panel px-4 sm:px-6 py-2.5 sm:py-3 rounded-2xl flex items-center gap-3 sm:gap-4 border border-[#00f0ff]/40 shadow-[0_0_20px_rgba(0,240,255,0.2)] animate-fade-in pointer-events-none max-w-[90vw]"
    >
      <div class="w-3 h-3 rounded-full animate-ping shrink-0" :style="{ backgroundColor: hoveredSkill.color }"></div>
      <div class="truncate">
        <h4 class="text-white font-bold text-sm sm:text-base flex items-center gap-2 truncate">
          <span class="truncate">{{ hoveredSkill.name }}</span>
          <span class="text-[10px] sm:text-xs px-2 py-0.5 rounded-full bg-white/10 text-[#00f0ff] font-mono shrink-0">
            {{ hoveredSkill.proficiency }}% Mastery
          </span>
        </h4>
        <p class="text-[10px] sm:text-xs text-slate-300 truncate">
          {{ hoveredSkill.years_of_experience }} Yrs Exp • {{ hoveredSkill.projects_count }}+ Projects
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import * as THREE from 'three';
import { usePortfolioStore, type Skill } from '@/Stores/usePortfolioStore';

const portfolioStore = usePortfolioStore();
const mountContainer = ref<HTMLDivElement | null>(null);
const hoveredSkill = ref<Skill | null>(null);

let scene: THREE.Scene;
let camera: THREE.PerspectiveCamera;
let renderer: THREE.WebGLRenderer;
let mainGroup: THREE.Group;
let animationFrameId: number;

const skillMeshes: Array<{ mesh: THREE.Mesh; skill: Skill; angle: number; radius: number; speed: number }> = [];

const updateCameraZ = () => {
  if (!camera) return;
  if (window.innerWidth < 480) {
    camera.position.z = 12.5;
  } else if (window.innerWidth < 768) {
    camera.position.z = 10.5;
  } else {
    camera.position.z = 8.5;
  }
};

const initGalaxy = () => {
  if (!mountContainer.value) return;

  const width = mountContainer.value.clientWidth;
  const height = mountContainer.value.clientHeight;

  scene = new THREE.Scene();
  camera = new THREE.PerspectiveCamera(60, width / height, 0.1, 1000);
  updateCameraZ();

  renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
  renderer.setSize(width, height);
  renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
  mountContainer.value.appendChild(renderer.domElement);

  mainGroup = new THREE.Group();
  scene.add(mainGroup);

  // Central Glowing Core Globe
  const coreGeo = new THREE.IcosahedronGeometry(1.6, 3);
  const coreMat = new THREE.MeshBasicMaterial({
    color: 0x00f0ff,
    wireframe: true,
    transparent: true,
    opacity: 0.35,
  });
  const coreMesh = new THREE.Mesh(coreGeo, coreMat);
  mainGroup.add(coreMesh);

  // Core Inner Core
  const innerCoreGeo = new THREE.SphereGeometry(1.0, 32, 32);
  const innerCoreMat = new THREE.MeshBasicMaterial({
    color: 0x8b5cf6,
    transparent: true,
    opacity: 0.6,
  });
  const innerCoreMesh = new THREE.Mesh(innerCoreGeo, innerCoreMat);
  mainGroup.add(innerCoreMesh);

  // Orbit Rings & Tech Planet Nodes
  const skillsList = portfolioStore.skills.length > 0 ? portfolioStore.skills : [
    { id: 1, name: 'Laravel 12', proficiency: 98, orbit_radius: 3.0, color: '#ff2d20', years_of_experience: 3, projects_count: 12 },
    { id: 2, name: 'Filament v4', proficiency: 96, orbit_radius: 3.8, color: '#f59e0b', years_of_experience: 3, projects_count: 8 },
    { id: 3, name: 'Vue 3', proficiency: 95, orbit_radius: 4.6, color: '#42b883', years_of_experience: 3, projects_count: 10 },
    { id: 4, name: 'Claude Code AI', proficiency: 98, orbit_radius: 5.4, color: '#ec4899', years_of_experience: 2, projects_count: 15 },
    { id: 5, name: 'TypeScript', proficiency: 90, orbit_radius: 6.2, color: '#3178c6', years_of_experience: 2, projects_count: 8 },
    { id: 6, name: 'MySQL / Database', proficiency: 94, orbit_radius: 7.0, color: '#00758f', years_of_experience: 3, projects_count: 14 },
  ];

  skillsList.forEach((sk, idx) => {
    const radius = sk.orbit_radius || 3.0 + idx * 0.8;
    const angle = (idx / skillsList.length) * Math.PI * 2;

    // Draw orbit path line
    const curve = new THREE.EllipseCurve(0, 0, radius, radius, 0, 2 * Math.PI, false, 0);
    const points = curve.getPoints(64);
    const orbitGeo = new THREE.BufferGeometry().setFromPoints(points.map(p => new THREE.Vector3(p.x, 0, p.y)));
    const orbitMat = new THREE.LineBasicMaterial({ color: 0xffffff, transparent: true, opacity: 0.1 });
    const orbitLine = new THREE.Line(orbitGeo, orbitMat);
    mainGroup.add(orbitLine);

    // Planet Node Mesh
    const planetGeo = new THREE.SphereGeometry(0.35, 24, 24);
    const planetMat = new THREE.MeshStandardMaterial({
      color: new THREE.Color(sk.color || '#00f0ff'),
      roughness: 0.2,
      metalness: 0.8,
      emissive: new THREE.Color(sk.color || '#00f0ff'),
      emissiveIntensity: 0.4,
    });
    const planetMesh = new THREE.Mesh(planetGeo, planetMat);

    planetMesh.position.x = Math.cos(angle) * radius;
    planetMesh.position.z = Math.sin(angle) * radius;

    mainGroup.add(planetMesh);

    skillMeshes.push({
      mesh: planetMesh,
      skill: sk as Skill,
      angle: angle,
      radius: radius,
      speed: 0.005 + idx * 0.001,
    });
  });

  // Ambient & Point Lighting
  const light1 = new THREE.DirectionalLight(0xffffff, 1.5);
  light1.position.set(5, 10, 7);
  scene.add(light1);

  const light2 = new THREE.PointLight(0x00f0ff, 2, 20);
  light2.position.set(0, 0, 0);
  scene.add(light2);

  // Raycaster for Hover & Touch Interaction
  const raycaster = new THREE.Raycaster();
  const mouse = new THREE.Vector2();

  const handlePointer = (clientX: number, clientY: number) => {
    if (!mountContainer.value) return;
    const rect = mountContainer.value.getBoundingClientRect();
    mouse.x = ((clientX - rect.left) / rect.width) * 2 - 1;
    mouse.y = -((clientY - rect.top) / rect.height) * 2 + 1;

    raycaster.setFromCamera(mouse, camera);
    const intersects = raycaster.intersectObjects(skillMeshes.map(s => s.mesh));

    if (intersects.length > 0) {
      const hitMesh = intersects[0].object as THREE.Mesh;
      const found = skillMeshes.find(s => s.mesh === hitMesh);
      if (found) {
        hoveredSkill.value = found.skill;
        hitMesh.scale.set(1.4, 1.4, 1.4);
      }
    } else {
      skillMeshes.forEach(s => s.mesh.scale.set(1, 1, 1));
      hoveredSkill.value = null;
    }
  };

  mountContainer.value.addEventListener('mousemove', (e) => handlePointer(e.clientX, e.clientY));
  mountContainer.value.addEventListener('touchstart', (e) => {
    if (e.touches.length > 0) {
      handlePointer(e.touches[0].clientX, e.touches[0].clientY);
    }
  }, { passive: true });

  window.addEventListener('resize', onResize);

  animate();
};

const onResize = () => {
  if (!mountContainer.value || !camera || !renderer) return;
  const width = mountContainer.value.clientWidth;
  const height = mountContainer.value.clientHeight;
  camera.aspect = width / height;
  updateCameraZ();
  camera.updateProjectionMatrix();
  renderer.setSize(width, height);
};

const animate = () => {
  animationFrameId = requestAnimationFrame(animate);

  if (mainGroup) {
    mainGroup.rotation.y += 0.002;
  }

  skillMeshes.forEach(item => {
    item.angle += item.speed;
    item.mesh.position.x = Math.cos(item.angle) * item.radius;
    item.mesh.position.z = Math.sin(item.angle) * item.radius;
  });

  renderer.render(scene, camera);
};

onMounted(() => {
  initGalaxy();
});

onUnmounted(() => {
  cancelAnimationFrame(animationFrameId);
  window.removeEventListener('resize', onResize);
  if (renderer && renderer.domElement) {
    renderer.domElement.remove();
  }
});
</script>
