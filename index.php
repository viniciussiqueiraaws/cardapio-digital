<?php
$host   = getenv('DB_HOST');
$dbname = getenv('DB_NAME');
$user   = getenv('DB_USER');
$pass   = getenv('DB_PASS');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pratos = $pdo->query("SELECT nome, descricao, preco, imagem_url FROM pratos ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CardápioDigital</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: 'Georgia', serif;
      background: #0f0f0f;
      color: #f0ece4;
      min-height: 100vh;
    }

    /* HERO */
    .hero {
      position: relative;
      height: 420px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
      overflow: hidden;
    }
    .hero-bg {
      position: absolute;
      inset: 0;
      background-image: url('https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=1400&q=80');
      background-size: cover;
      background-position: center;
      filter: brightness(0.35);
    }
    .hero-overlay {
      position: absolute;
      inset: 0;
      background: linear-gradient(to bottom, rgba(0,0,0,0.2) 0%, rgba(0,0,0,0.6) 100%);
    }
    .hero-content { position: relative; z-index: 1; padding: 0 20px; }
    .hero-badge {
      display: inline-block;
      font-family: Arial, sans-serif;
      font-size: 0.68rem;
      letter-spacing: 0.25em;
      text-transform: uppercase;
      color: #e67e22;
      border: 1px solid #e67e22;
      padding: 5px 16px;
      border-radius: 20px;
      margin-bottom: 18px;
    }
    .hero h1 {
      font-size: clamp(2.2rem, 6vw, 4rem);
      font-weight: normal;
      letter-spacing: 0.06em;
      color: #fff;
      line-height: 1.1;
      text-shadow: 0 2px 20px rgba(0,0,0,0.5);
    }
    .hero h1 span { color: #e67e22; }
    .hero-sub {
      font-family: Arial, sans-serif;
      font-size: 0.88rem;
      color: #c0b0a0;
      margin-top: 12px;
      letter-spacing: 0.1em;
    }
    .divider {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 14px;
      margin-top: 22px;
    }
    .divider-line { width: 60px; height: 1px; background: rgba(230,126,34,0.4); }
    .divider-icon { color: #e67e22; font-size: 0.9rem; }

    /* NAV */
    nav {
      background: #141414;
      border-bottom: 1px solid #222;
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      padding: 0 16px;
      position: sticky;
      top: 0;
      z-index: 100;
    }
    nav a {
      font-family: Arial, sans-serif;
      font-size: 0.75rem;
      letter-spacing: 0.1em;
      text-transform: uppercase;
      color: #7a6a5a;
      text-decoration: none;
      padding: 15px 16px;
      border-bottom: 2px solid transparent;
      transition: color 0.2s, border-color 0.2s;
    }
    nav a:hover, nav a.active { color: #e67e22; border-bottom-color: #e67e22; }

    /* LAYOUT */
    main { max-width: 980px; margin: 0 auto; padding: 52px 20px 80px; }

    .section-label {
      font-family: Arial, sans-serif;
      font-size: 0.65rem;
      letter-spacing: 0.3em;
      text-transform: uppercase;
      color: #e67e22;
      margin-bottom: 6px;
    }
    .section-heading {
      font-size: 1.8rem;
      font-weight: normal;
      color: #f0ece4;
      margin-bottom: 32px;
    }

    /* GRID DE PRATOS */
    .pratos-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 20px;
    }

    .card {
      background: #181818;
      border: 1px solid #2a2a2a;
      border-radius: 14px;
      overflow: hidden;
      transition: transform 0.25s, border-color 0.25s, box-shadow 0.25s;
    }
    .card:hover {
      transform: translateY(-4px);
      border-color: #e67e22;
      box-shadow: 0 12px 40px rgba(230,126,34,0.12);
    }
    .card-img {
      width: 100%;
      height: 190px;
      object-fit: cover;
      display: block;
      background: #1e1e1e;
    }
    .card-img-placeholder {
      width: 100%;
      height: 190px;
      background: linear-gradient(135deg, #1e1408, #2a1c0a);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 3rem;
    }
    .card-body { padding: 20px; }
    .card-nome { font-size: 1.1rem; color: #f5efe6; margin-bottom: 8px; }
    .card-desc {
      font-family: Arial, sans-serif;
      font-size: 0.82rem;
      color: #6a5a4a;
      line-height: 1.6;
      margin-bottom: 16px;
    }
    .card-footer {
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-top: 1px solid #222;
      padding-top: 14px;
    }
    .preco-label {
      font-family: Arial, sans-serif;
      font-size: 0.62rem;
      color: #4a3a2a;
      display: block;
      margin-bottom: 2px;
    }
    .preco { font-size: 1.2rem; color: #e67e22; }

    /* EMPTY STATE */
    .empty {
      text-align: center;
      padding: 80px 20px;
      font-family: Arial, sans-serif;
      color: #4a3a2a;
    }
    .empty p { margin-top: 12px; font-size: 0.85rem; }

    footer {
      text-align: center;
      padding: 40px 20px;
      border-top: 1px solid #1a1a1a;
      font-family: Arial, sans-serif;
      font-size: 0.73rem;
      color: #3a3a3a;
      letter-spacing: 0.04em;
      line-height: 2;
    }
    footer span { color: #4a3a2a; }
  </style>
</head>
<body>

  <header class="hero">
    <div class="hero-bg"></div>
    <div class="hero-overlay"></div>
    <div class="hero-content">
      <div class="hero-badge">✦ Desde 1987 . v2.0 ✦</div>
      <h1>Cardápio<span>Digital</span></h1>
      <p class="hero-sub">Cozinha italiana autêntica · São Paulo, SP</p>
      <div class="divider">
        <div class="divider-line"></div>
        <div class="divider-icon">🍽️</div>
        <div class="divider-line"></div>
      </div>
    </div>
  </header>

  <nav>
    <a href="#" class="active">Cardápio</a>
    <a href="#">Entradas</a>
    <a href="#">Massas</a>
    <a href="#">Grelhados</a>
    <a href="#">Bebidas</a>
    <a href="#">Sobremesas</a>
  </nav>

  <main>
    <div class="section-label">— Do nosso banco de dados para sua mesa</div>
    <h2 class="section-heading">Nossos Pratos</h2>

    <?php if (empty($pratos)): ?>
      <div class="empty">
        <div style="font-size:2.5rem;">🍽️</div>
        <p>Nenhum prato cadastrado ainda.</p>
      </div>
    <?php else: ?>
      <div class="pratos-grid">
        <?php foreach ($pratos as $prato): ?>
          <div class="card">
            <?php if (!empty($prato['imagem_url'])): ?>
              <img class="card-img"
                src="<?= htmlspecialchars($prato['imagem_url']) ?>"
                alt="<?= htmlspecialchars($prato['nome']) ?>">
            <?php else: ?>
              <div class="card-img-placeholder">🍽️</div>
            <?php endif; ?>
            <div class="card-body">
              <h2 class="card-nome"><?= htmlspecialchars($prato['nome']) ?></h2>
              <p class="card-desc"><?= htmlspecialchars($prato['descricao']) ?></p>
              <div class="card-footer">
                <div>
                  <span class="preco-label">por pessoa</span>
                  <span class="preco">R$ <?= number_format($prato['preco'], 2, ',', '.') ?></span>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </main>

  <footer>
    <p>🍽️ &nbsp;CardápioDigital · Rua Augusta, 1420 · São Paulo · (11) 3456-7890</p>
    <p>Aberto de terça a domingo · 12h às 15h e 19h às 23h</p>
    <p style="margin-top:12px;"><span>Cardápio digital hospedado na AWS · cardapiodigitalaws.com.br</span></p>
  </footer>

</body>
</html>
