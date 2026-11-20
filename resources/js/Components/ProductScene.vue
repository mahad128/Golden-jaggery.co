<script setup>
import { onMounted, onUnmounted, ref, watch } from 'vue';

const props = defineProps({
    kind: { type: String, default: 'gur' },
    images: { type: Array, default: () => [] },
    label: { type: String, default: 'Fresh gur' },
    photo: { type: String, default: '' },
    fallback: { type: String, default: '/images/gur-block-cutout.svg' },
});

const host = ref(null);
const sceneError = ref('');
let THREE;
let renderer;
let scene;
let camera;
let heroGroup;
let frame;
let resizeObserver;
let visibilityObserver;
let pointerX = 0;
let pointerY = 0;
let tiltX = 0;
let tiltY = 0;
let reducedMotion = false;
let hasAppeared = false;
let sceneGeneration = 0;
let cameraDistance = 5.3;
const leaves = [];

function createLeaf(color, x, y, z, rotation) {
    const shape = new THREE.Shape();
    shape.moveTo(0, 0);
    shape.quadraticCurveTo(0.18, 0.24, 0.04, 0.72);
    shape.quadraticCurveTo(-0.15, 0.34, 0, 0);
    const leaf = new THREE.Mesh(
        new THREE.ShapeGeometry(shape),
        new THREE.MeshStandardMaterial({ color, side: THREE.DoubleSide, roughness: 0.7 }),
    );
    leaf.position.set(x, y, z);
    leaf.rotation.set(rotation, rotation * 0.4, rotation * 0.2);
    heroGroup.add(leaf);
    leaves.push(leaf);
}

async function initialize() {
    if (!host.value) return;
    const generation = ++sceneGeneration;
    THREE = await import('three');
    if (!host.value || generation !== sceneGeneration) return;

    reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    sceneError.value = '';
    scene = new THREE.Scene();
    scene.background = null;
    camera = new THREE.PerspectiveCamera(36, host.value.clientWidth / host.value.clientHeight, 0.1, 100);
    camera.position.set(0, 0.2, cameraDistance);
    renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true, powerPreference: 'low-power' });
    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 1.6));
    renderer.setSize(host.value.clientWidth, host.value.clientHeight);
    renderer.outputColorSpace = THREE.SRGBColorSpace;
    host.value.appendChild(renderer.domElement);

    scene.add(new THREE.AmbientLight(0xffedc5, 2.4));
    const keyLight = new THREE.DirectionalLight(0xffffff, 3.2);
    keyLight.position.set(3, 5, 5);
    scene.add(keyLight);
    const fillLight = new THREE.PointLight(0xe4a135, 20, 15);
    fillLight.position.set(-3, -1, 3);
    scene.add(fillLight);

    heroGroup = new THREE.Group();
    scene.add(heroGroup);

    if (props.photo) {
        const texture = await new Promise((resolve, reject) => {
            new THREE.TextureLoader().load(props.photo, resolve, undefined, reject);
        });
        if (!host.value || generation !== sceneGeneration) {
            texture.dispose();
            return;
        }
        texture.colorSpace = THREE.SRGBColorSpace;
        const imageAspect = texture.image.width / texture.image.height;
        const visibleHeight = 2 * cameraDistance * Math.tan(THREE.MathUtils.degToRad(camera.fov / 2));
        const visibleWidth = visibleHeight * camera.aspect;
        const planeHeight = Math.min(visibleHeight * 0.84, (visibleWidth * 0.84) / imageAspect);
        const product = new THREE.Mesh(
            new THREE.PlaneGeometry(planeHeight * imageAspect, planeHeight),
            new THREE.MeshBasicMaterial({ map: texture, transparent: true, alphaTest: 0.01, side: THREE.DoubleSide, toneMapped: false }),
        );
        heroGroup.add(product);
    } else if (props.kind === 'pile') {
        const gold = new THREE.MeshStandardMaterial({ color: '#c98a2b', roughness: 0.28, metalness: 0.12 });
        const darkGold = new THREE.MeshStandardMaterial({ color: '#9b5b19', roughness: 0.45 });
        for (let index = 0; index < 14; index += 1) {
            const radius = 0.22 + Math.random() * 0.16;
            const crystal = new THREE.Mesh(new THREE.IcosahedronGeometry(radius, 1), index % 3 ? gold : darkGold);
            crystal.position.set((Math.random() - 0.5) * 1.5, (Math.random() - 0.5) * 0.9, (Math.random() - 0.5) * 0.6);
            crystal.rotation.set(Math.random() * 3, Math.random() * 3, Math.random() * 3);
            heroGroup.add(crystal);
        }
    } else {
        const gold = new THREE.MeshStandardMaterial({ color: '#c98a2b', roughness: 0.28, metalness: 0.12 });
        const block = new THREE.Mesh(new THREE.BoxGeometry(1.7, 1.25, 1.05, 5, 5, 5), gold);
        block.rotation.set(-0.16, 0.4, 0.08);
        heroGroup.add(block);
        const edge = new THREE.EdgesGeometry(block.geometry);
        heroGroup.add(new THREE.LineSegments(edge, new THREE.LineBasicMaterial({ color: '#edc677', transparent: true, opacity: 0.48 })));
    }

    createLeaf('#456837', -1.6, 0.65, -0.5, 0.28);
    createLeaf('#668447', 1.55, -0.7, -0.2, -0.5);
    createLeaf('#35572f', 1.7, 0.9, -0.7, 0.92);

    resizeObserver = new ResizeObserver(() => {
        if (!host.value || !renderer || !camera) return;
        const width = host.value.clientWidth;
        const height = host.value.clientHeight;
        camera.aspect = width / height;
        camera.updateProjectionMatrix();
        renderer.setSize(width, height);
    });
    resizeObserver.observe(host.value);
    frame = requestAnimationFrame(animate);
}

function startScene() {
    initialize().catch((error) => {
        sceneError.value = 'The 3D preview is unavailable in this browser. Product photos are still available.';
        console.error('Unable to load the product 3D preview.', error);
    });
}

function animate(time) {
    if (!renderer || !heroGroup) return;
    if (!reducedMotion) {
        heroGroup.rotation.y += (pointerX * 0.16 + tiltY * 0.04 - heroGroup.rotation.y) * 0.035;
        heroGroup.rotation.x += (pointerY * 0.12 + tiltX * 0.035 - heroGroup.rotation.x) * 0.035;
        heroGroup.position.y = Math.sin(time * 0.0007) * 0.055;
        leaves.forEach((leaf, index) => { leaf.rotation.z = Math.sin(time * 0.0005 + index) * 0.16; });
    }
    renderer.render(scene, camera);
    frame = requestAnimationFrame(animate);
}

function onPointerMove(event) {
    const bounds = host.value?.getBoundingClientRect();
    if (!bounds) return;
    pointerX = ((event.clientX - bounds.left) / bounds.width - 0.5) * 2;
    pointerY = -((event.clientY - bounds.top) / bounds.height - 0.5) * 2;
}

function onDeviceTilt(event) {
    tiltX = Math.max(-1, Math.min(1, (event.beta ?? 0) / 30));
    tiltY = Math.max(-1, Math.min(1, (event.gamma ?? 0) / 30));
}

function zoomIn() {
    cameraDistance = Math.max(3.2, cameraDistance - 0.45);
    if (camera) camera.position.z = cameraDistance;
}

function zoomOut() {
    cameraDistance = Math.min(7.5, cameraDistance + 0.45);
    if (camera) camera.position.z = cameraDistance;
}

function disposeScene() {
    cancelAnimationFrame(frame);
    sceneGeneration += 1;
    resizeObserver?.disconnect();
    resizeObserver = null;
    if (renderer) {
        renderer.dispose();
        renderer.forceContextLoss();
        renderer.domElement.remove();
        renderer = null;
    }
    scene?.traverse((object) => {
        object.geometry?.dispose();
        if (Array.isArray(object.material)) {
            object.material.forEach((material) => {
                material.map?.dispose();
                material.dispose();
            });
        } else if (object.material) {
            object.material.map?.dispose();
            object.material.dispose();
        }
    });
    scene = null;
    camera = null;
    heroGroup = null;
}

onMounted(() => {
    host.value?.addEventListener('pointermove', onPointerMove, { passive: true });
    window.addEventListener('deviceorientation', onDeviceTilt, { passive: true });
    visibilityObserver = new IntersectionObserver((entries) => {
        if (entries.some((entry) => entry.isIntersecting)) {
            hasAppeared = true;
            visibilityObserver?.disconnect();
            startScene();
        }
    }, { rootMargin: '120px' });
    if (host.value) visibilityObserver.observe(host.value);
});
onUnmounted(() => {
    visibilityObserver?.disconnect();
    host.value?.removeEventListener('pointermove', onPointerMove);
    window.removeEventListener('deviceorientation', onDeviceTilt);
    disposeScene();
});

watch(() => props.kind, () => {
    disposeScene();
    leaves.length = 0;
    cameraDistance = 5.3;
    if (hasAppeared) startScene();
});

defineExpose({ zoomIn, zoomOut });
</script>

<template>
    <div ref="host" class="relative h-full min-h-[280px] w-full" role="img" :aria-label="`${label}, interactive 3D product preview`">
        <img v-if="sceneError" :src="fallback" :alt="label" class="scene-fallback" loading="eager">
        <p v-if="sceneError" class="absolute inset-x-4 bottom-3 z-10 rounded-xl bg-[var(--paper)]/90 p-3 text-center text-xs text-stone-600">{{ sceneError }}</p>
    </div>
</template>
