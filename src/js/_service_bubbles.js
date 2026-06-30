import Matter from "matter-js";

let servicesData = []; // Variable to store fetched data

function decodeHtmlEntities(text) {
  const element = document.createElement("div");
  element.innerHTML = text;
  return element.innerText || element.textContent;
}

const canvasContainer = document.getElementById("canvas-container");
const canvas = document.getElementById("canvas");

function noopUpdateCircles() {}

let updateCircles = noopUpdateCircles;

if (canvasContainer && canvas) {
canvasContainer.style.display = "block";

const ctx = canvas.getContext("2d");

function resizeCanvas() {
  canvas.width = canvasContainer.clientWidth;
  canvas.height = canvasContainer.clientHeight;
}

let resizeTimeout;
window.addEventListener("resize", () => {
  clearTimeout(resizeTimeout);
  resizeCanvas();
  updateBounds();
  resizeTimeout = setTimeout(() => {
    updateCircles();
  }, 300);
});

resizeCanvas();

const {
  Engine,
  Render,
  Runner,
  Bodies,
  Composite,
  Mouse,
  MouseConstraint,
  Events,
  World,
} = Matter;

const engine = Engine.create();
const world = engine.world;

const render = Render.create({
  canvas: canvas,
  engine: engine,
  options: {
    width: canvas.width,
    height: canvas.height,
    wireframes: false,
    background: "transparent",
  },
});

Render.run(render);
Runner.run(Runner.create(), engine);

const bounds = {
  left: Bodies.rectangle(-50, canvas.height / 2, 100, canvas.height, {
    isStatic: true,
  }),
  right: Bodies.rectangle(
    canvas.width + 50,
    canvas.height / 2,
    100,
    canvas.height,
    { isStatic: true }
  ),
  top: Bodies.rectangle(canvas.width / 2, -50, canvas.width, 100, {
    isStatic: true,
  }),
  bottom: Bodies.rectangle(
    canvas.width / 2,
    canvas.height + 50,
    canvas.width,
    100,
    { isStatic: true }
  ),
};

Composite.add(world, [bounds.left, bounds.right, bounds.top, bounds.bottom]);

function getRadius() {
  return Math.min(canvas.width, canvas.height) * 0.18;
}

let radius = getRadius();

const circles = [];

function createCircles() {
  circles.forEach((circle) => {
    World.remove(world, circle);
  });
  circles.length = 0;
  radius = getRadius();

  servicesData.forEach((service) => {
    const text = decodeHtmlEntities(service.title.rendered).toUpperCase();
    const uiColor =
      service.acf && service.acf.ui_color ? service.acf.ui_color : "#A5F953";
    const bgColor =
      service.acf && service.acf.service_bg_color
        ? service.acf.service_bg_color
        : "#FFFFFF";

    const circle = Bodies.circle(
      Math.random() * canvas.width,
      (Math.random() * canvas.height) / 2,
      radius,
      {
        restitution: 1,
        angle: Math.random() * 2 * Math.PI,
        label: text,
        render: {
          fillStyle: "#000000",
        },
      }
    );

    circle.originalRadius = radius;
    circle.targetRadius = radius;
    circle.uiColor = uiColor;
    circle.bgColor = bgColor;
    circles.push(circle);
    Composite.add(world, circle);
  });
}

const mouse = Mouse.create(canvas);
const mouseConstraint = MouseConstraint.create(engine, {
  mouse: mouse,
  constraint: {
    stiffness: 0.2,
    render: {
      visible: false,
    },
  },
});

Composite.add(world, mouseConstraint);

mouseConstraint.mouse.element.removeEventListener(
  "mousewheel",
  mouseConstraint.mouse.mousewheel
);
mouseConstraint.mouse.element.removeEventListener(
  "DOMMouseScroll",
  mouseConstraint.mouse.mousewheel
);

render.mouse = mouse;

let isDragging = false;
let lastClickTime = 0;

function isInsideCircle(position, circle) {
  const distance = Math.sqrt(
    Math.pow(position.x - circle.position.x, 2) +
      Math.pow(position.y - circle.position.y, 2)
  );
  return distance <= circle.targetRadius;
}

Events.on(mouseConstraint, "startdrag", function (event) {
  const mousePosition = mouse.position;
  const isInsideAnyCircle = circles.some((circle) =>
    isInsideCircle(mousePosition, circle)
  );
  if (!isInsideAnyCircle) {
    mouseConstraint.constraint.bodyB = null;
    mouseConstraint.constraint.pointB = null;
    mouseConstraint.constraint.pointA = null;
  } else {
    isDragging = true;
  }
});

Events.on(mouseConstraint, "enddrag", function (event) {
  setTimeout(() => {
    isDragging = false;
  }, 100);
});

canvas.addEventListener("mousedown", (event) => {
  lastClickTime = event.timeStamp;
});

canvas.addEventListener("mouseup", (event) => {
  if (event.timeStamp - lastClickTime < 200) {
    handleCanvasClick(event);
  }
});

function handleCanvasClick(event) {
  if (!isDragging) {
    const mousePosition = { x: event.clientX, y: event.clientY };
    circles.forEach((circle) => {
      const distance = Math.sqrt(
        Math.pow(mousePosition.x - circle.position.x, 2) +
          Math.pow(mousePosition.y - circle.position.y, 2)
      );
      if (distance <= circle.targetRadius) {
        alert(`Clicked on ${circle.label}`);
      }
    });
  }
}

function updateBounds() {
  World.remove(world, [bounds.left, bounds.right, bounds.top, bounds.bottom]);

  bounds.left = Bodies.rectangle(-50, canvas.height / 2, 100, canvas.height, {
    isStatic: true,
  });
  bounds.right = Bodies.rectangle(
    canvas.width + 50,
    canvas.height / 2,
    100,
    canvas.height,
    { isStatic: true }
  );
  bounds.top = Bodies.rectangle(canvas.width / 2, -50, canvas.width, 100, {
    isStatic: true,
  });
  bounds.bottom = Bodies.rectangle(
    canvas.width / 2,
    canvas.height + 50,
    canvas.width,
    100,
    { isStatic: true }
  );

  World.add(world, [bounds.left, bounds.right, bounds.top, bounds.bottom]);
}

function updateCirclesImpl() {
  radius = getRadius();
  createCircles();
}
updateCircles = updateCirclesImpl;

function wrapText(ctx, text, x, y, maxWidth, lineHeight) {
  const words = text.split(" ");
  let line = "";
  let lines = [];

  for (let n = 0; n < words.length; n++) {
    const testLine = line + words[n] + " ";
    const metrics = ctx.measureText(testLine);
    const testWidth = metrics.width;

    if (testWidth > maxWidth && n > 0) {
      lines.push(line.trim());
      line = words[n] + " ";
    } else {
      line = testLine;
    }
  }
  lines.push(line.trim());

  const totalHeight = lines.length * lineHeight;
  const startY = y - totalHeight / 2 + lineHeight / 2;

  for (let k = 0; k < lines.length; k++) {
    ctx.fillText(lines[k], x, startY + k * lineHeight);
  }
}

function lerp(start, end, amt) {
  return (1 - amt) * start + amt * end;
}

function draw() {
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  const mousePosition = mouse.position;
  let isAnyHovered = false;

  circles.forEach((circle, index) => {
    const distance = Math.sqrt(
      Math.pow(mousePosition.x - circle.position.x, 2) +
        Math.pow(mousePosition.y - circle.position.y, 2)
    );
    const isHovered = distance <= radius;

    if (isHovered) {
      circle.targetRadius = lerp(circle.targetRadius, radius * 1.2, 0.1);
      isAnyHovered = true;
    } else {
      circle.targetRadius = lerp(
        circle.targetRadius,
        circle.originalRadius,
        0.1
      );
    }

    ctx.save();
    ctx.translate(circle.position.x, circle.position.y);
    ctx.rotate(circle.angle);
    ctx.beginPath();
    ctx.arc(0, 0, circle.targetRadius, 0, 2 * Math.PI);
    ctx.fillStyle = isHovered ? circle.bgColor : "#fff";
    ctx.fill();
    ctx.closePath();

    ctx.fillStyle = isHovered ? circle.uiColor : "black";
    ctx.font = `${circle.targetRadius / 5}px 'UglyDave', sans-serif`;
    ctx.textAlign = "center";
    ctx.textBaseline = "middle";
    wrapText(
      ctx,
      circle.label,
      0,
      0,
      circle.targetRadius * 1.8,
      circle.targetRadius / 5
    );
    ctx.restore();
  });

  requestAnimationFrame(draw);
}

// Fetch data and create circles
fetch("/wp-json/wp/v2/services?per_page=100")
  .then((response) => response.json())
  .then((data) => {
    servicesData = data; // Store data
    createCircles(); // Create circles initially
  })
  .catch((error) => {
    console.error("Error fetching services:", error);
  });

draw();
}

export { updateCircles };
