{
  stdenv,
  php,
  poppler_utils,
  pdf_url,
  domain,
  author,
}:
stdenv.mkDerivation {
  pname = "bakule-timer";
  version = "1.0.0";
  src = ./.;

  buildInputs = [php poppler_utils];

  installPhase = ''
    mkdir -p $out/share/bakule-timer
    cp index.html metrics.php stats.php $out/share/bakule-timer/
    substituteInPlace $out/share/bakule-timer/stats.php \
      --replace "pdftotext" "${poppler_utils}/bin/pdftotext" \
      --replace "<PDF_URL>" "${pdf_url}"
    substituteInPlace $out/share/bakule-timer/metrics.php \
      --replace "<DOMAIN>" "${domain}" \
      --replace "<AUTHOR>" "${author}"
  '';

  meta = {
    description = "Countdown webpage for bachelor thesis sourced from ${pdf_url}";
    homepage = "https://bc.kde.dev-null.me";
  };
}
