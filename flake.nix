{
  inputs = {
    nixpkgs.url = "github:NixOS/nixpkgs/nixos-unstable";
    flake-utils.url = "github:numtide/flake-utils";
  };

  outputs = {
    self,
    nixpkgs,
    flake-utils,
    ...
  }:
    flake-utils.lib.eachDefaultSystem (system: let
      pkgs = nixpkgs.legacyPackages.${system};
      popplerUtils = pkgs.poppler_utils;
    in {
      packages.bakule-timer = pkgs.stdenv.mkDerivation {
        pname = "bakule-timer";
        version = "1.0.0";
        src = ./.;

        buildInputs = [
          pkgs.php
          popplerUtils
        ];

        installPhase = ''
          mkdir -p $out/share/bakule-timer
          cp index.html $out/share/bakule-timer/
          cp stats.php      $out/share/bakule-timer/
          substituteInPlace $out/share/bakule-timer/stats.php \
            --replace "pdftotext" "${popplerUtils}/bin/pdftotext"
        '';

        meta = {
          description = "Countdown webpage for bachelor thesis";
          homepage = "https://bc.kde.dev-null.me";
        };
      };
      defaultPackage = self.packages.${system}.bakule-timer;
    });
}
