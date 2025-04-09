const formulario = document.getElementById("formulario-cadastro");

formulario.addEventListener('submit', (event) => {
	const nome = document.getElementById("nome").value.trim();
	const email = document.getElementById("email").value.trim();
	const senha = document.getElementById("senha").value;

	const nomeDividido = nome.split(" ");
	if (nomeDividido.length < 2)
	{
		alert ("Por favor, insira o nome completo (pelo menos um sobrenome)");
		event.preventDefault();
		return;
	}

	if (senha.length < 6) {
		alert('A senha precisa ter no mínimo 6 caracteres.');
		event.preventDefault();
		return;
	}
})