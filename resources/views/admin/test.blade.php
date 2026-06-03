<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<style>
    /* DARK OVERLAY */
#modalOverlay{
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.5);
  z-index: 999;
}

/* MODAL BOX */
#sampleModal{
  display: none;
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 380px;
  max-width: 90%;
  background: #fff;
  padding: 25px;
  border-radius: 12px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.2);
  z-index: 1000;
}

/* TITLE */
#sampleModal h3{
  margin-bottom: 15px;
  font-size: 22px;
  text-align: center;
}

/* INPUTS + SELECT */
#sampleModal select,
#sampleModal input{
  width: 100%;
  padding: 10px;
  margin-top: 10px;
  border: 1px solid #ddd;
  border-radius: 6px;
  outline: none;
  box-sizing: border-box;
}

/* HIDDEN FORM SECTIONS */
.section{
  display: none;
  margin-top: 10px;
}

/* CREATE BUTTON */
#submitBtn{
  width: 100%;
  margin-top: 15px;
  padding: 10px;
  border: none;
  background: #0057ff;
  color: white;
  border-radius: 6px;
  cursor: pointer;
}

/* CANCEL BUTTON */
#closeBtn{
  width: 100%;
  margin-top: 10px;
  padding: 10px;
  border: none;
  background: #ef4444;
  color: white;
  border-radius: 6px;
  cursor: pointer;
}

/* HOVER EFFECT */
#submitBtn:hover{
  background: #0046d1;
}

#closeBtn:hover{
  background: #dc2626;
}
</style>
<body>
    <!-- ADD USER BUTTON -->
<button id="addUserBtn" onclick="openModal()">
  <i class="fa-solid fa-user-plus"></i> Add Users
</button>

<!-- DARK BACKGROUND -->
<div id="modalOverlay" onclick="closeModal()"></div>

<!-- MODAL BOX -->
<div id="sampleModal">

  <h3>Create User</h3>
  

  <!-- ROLE SELECT -->
  <select id="roleSelect">
    <option value="" selected disabled>-- Select Role --</option>
    <option value="admin">Admin</option>
    <option value="technician">Technician</option>
    <option value="employee">Employee</option>
  </select>
  <!-- FULL NAME -->
  <input type="text" placeholder="e.g. Marcus Thorne" />

  <!-- EMAIL -->
  <input type="email" placeholder="m.thorne@nexuscore.com" />

  <!-- PHONE NUMBER -->
  <input type="tel" placeholder="+1 (555) 000-0000" />

  <!-- SPECIALTY -->
  <select>
    <option value="" selected disabled>Select a primary discipline</option>
    <option value="networking">Networking</option>
    <option value="software">Software</option>
    <option value="hardware">Hardware</option>
  </select>


  <!-- BUTTONS -->
  <button id="submitBtn">Create User</button>
  <button id="closeBtn" onclick="closeModal()">Cancel</button>

</div>
   
<script>
const modal = document.getElementById("sampleModal");
const modalOverlay = document.getElementById("modalOverlay");
const closeBtn = document.getElementById("closeBtn");

const openModal = () =>{
  modalOverlay.style.display = "block";
  modal.style.display = "block";
}

const closeModal = () =>{
  modalOverlay.style.display = "none";
  modal.style.display = "none";
}
</script>
</body>
</html>
