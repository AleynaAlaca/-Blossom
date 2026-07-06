<script setup>
import { ref, computed, onMounted } from "vue";
import api from "@/services/api";


const newTodo = ref("");
const todos = ref([]);


const getTodos = async () => {
  const res = await api.get("/todos");
  todos.value = res.data;
};


async function addTodo() {
  const text = newTodo.value.trim();
  if (!text) return;

  await api.post("/todos", {
    title: text
  });

  newTodo.value = "";
  getTodos();
}


async function deleteTodo(id) {
  await api.delete(`/todos/${id}`);
  getTodos();
}


async function toggleTodo(todo) {
  await api.put(`/todos/${todo.id}`, {
    completed: !todo.completed
  });

  getTodos();
}


const remaining = computed(() =>
  todos.value.filter(t => !t.completed).length
);



onMounted(() => {
  getTodos();
});
</script>

<template>
  <div class="page">
    <div class="board">
      <header class="board-header">
        <div class="stub-icon" aria-hidden="true"></div>

        <div class="board-title">
          <h1>Yapılacaklar</h1>
          <p>görev fişleri</p>
        </div>
      </header>

      
      <form class="input-row" @submit.prevent="addTodo">
        <input
          v-model="newTodo"
          type="text"
          placeholder="Yeni görev yaz..."
        />

        <button type="submit" class="add-btn">
          Ekle
        </button>
      </form>

      
      <p v-if="todos.length === 0" class="empty-state">
        Henüz görev yok
      </p>

      
      <ul v-else class="ticket-list">
        <li
          v-for="todo in todos"
          :key="todo.id"
          class="ticket"
          :class="{ done: todo.completed }"
        >
          
          <button class="check" @click="toggleTodo(todo)">
            ✔
          </button>

         
          <span class="ticket-text">
            {{ todo.title }}
          </span>

          
          <button class="delete" @click="deleteTodo(todo.id)">
            X
          </button>
        </li>
      </ul>

      
      <footer v-if="todos.length" class="board-footer">
        {{ remaining }} / {{ todos.length }} görev kaldı
      </footer>
    </div>
  </div>
</template>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Inter:wght@400;500;600&display=swap");
@import url("https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Inter:wght@400;500;600&family=Caveat:wght@500&display=swap");

.page {
  min-height: 100vh;
  display: flex;
  justify-content: center;
  padding: 56px 20px;
  background: #241d1b;
  background-image:
    radial-gradient(circle at 1px 1px, rgba(255,255,255,0.035) 1px, transparent 0);
  background-size: 18px 18px;
  font-family: "Inter", sans-serif;
}

.board {
  width: 100%;
  max-width: 460px;
}

.board-header {
  display: flex;
  align-items: center;
  gap: 14px;
  margin-bottom: 24px;
}

.stub-icon {
  width: 44px;
  height: 44px;
  flex-shrink: 0;
  border-radius: 10px;
  background: #3e2f2a;
  color: #e8a33d;
  display: flex;
  align-items: center;
  justify-content: center;
}
.stub-icon svg { width: 24px; height: 24px; }

.board-title h1 {
  margin: 0;
  font-family: "DM Mono", monospace;
  font-weight: 500;
  font-size: 22px;
  letter-spacing: 0.5px;
  color: #f3ead9;
}
.board-title p {
  margin: 2px 0 0;
  font-size: 12.5px;
  letter-spacing: 1.5px;
  text-transform: uppercase;
  color: #8f8178;
}

.input-row {
  display: flex;
  gap: 10px;
  margin-bottom: 28px;
}

input {
  flex: 1;
  padding: 13px 16px;
  border-radius: 10px;
  border: 1px solid #4a3d37;
  background: #2d2521;
  color: #f3ead9;
  font-family: "DM Mono", monospace;
  font-size: 15px;
  outline: none;
  transition: border-color 0.15s ease;
}
input::placeholder { color: #756a62; }
input:focus { border-color: #e8a33d; }

.add-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 0 18px;
  border: none;
  border-radius: 10px;
  background: #c8501f;
  color: #fdf3e7;
  font-size: 14.5px;
  font-weight: 600;
  cursor: pointer;
  transition: transform 0.12s ease, background 0.15s ease;
}
.add-btn svg { width: 18px; height: 18px; }
.add-btn:hover { background: #d85a25; }
.add-btn:active { transform: scale(0.96); }

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 10px;
  padding: 48px 20px;
  color: #7a6d64;
  font-size: 14.5px;
  text-align: center;
  border: 1.5px dashed #4a3d37;
  border-radius: 14px;
}
.empty-state svg { width: 34px; height: 34px; opacity: 0.7; }

.ticket-list {
  list-style: none;
  margin: 0;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.ticket {
  position: relative;
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 14px;
  background: #f3ead9;
  border-radius: 8px;
  box-shadow: 0 2px 0 rgba(0,0,0,0.15);
}

.ticket::before,
.ticket::after {
  content: "";
  position: absolute;
  top: 50%;
  width: 14px;
  height: 14px;
  border-radius: 50%;
  background: #241d1b;
  transform: translateY(-50%);
}
.ticket::before { left: -7px; }
.ticket::after { right: -7px; }

.check {
  flex-shrink: 0;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  border: 2px solid #b8a894;
  background: transparent;
  color: #3e7c59;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: border-color 0.15s ease, background 0.15s ease;
}
.check svg { width: 15px; height: 15px; }
.ticket.done .check { border-color: #3e7c59; background: #e3ede6; }

.ticket-text {
  flex: 1;
  font-size: 15px;
  color: #3a2e27;
  word-break: break-word;
}
.ticket.done .ticket-text {
  text-decoration: line-through;
  color: #a99b8c;
}

.perf {
  width: 0;
  border-left: 1.5px dashed #cabda7;
  align-self: stretch;
  margin: -12px 0;
}

.delete {
  flex-shrink: 0;
  width: 30px;
  height: 30px;
  border: none;
  background: transparent;
  color: #b56a4a;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background 0.15s ease, color 0.15s ease;
}
.delete svg { width: 16px; height: 16px; }
.delete:hover { background: #fbe4d8; color: #a3421f; }

.board-footer {
  margin-top: 18px;
  text-align: center;
  font-family: "DM Mono", monospace;
  font-size: 12.5px;
  letter-spacing: 0.5px;
  color: #8f8178;
}

.stub-enter-active,
.stub-leave-active {
  transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
}
.stub-enter-from {
  opacity: 0;
  transform: translateY(-8px) scale(0.98);
}
.stub-leave-to {
  opacity: 0;
  transform: translateX(24px) rotate(2deg);
}
.stub-leave-active {
  position: absolute;
  width: calc(100% - 28px);
}
.ticket-list {
  position: relative;
}
</style>